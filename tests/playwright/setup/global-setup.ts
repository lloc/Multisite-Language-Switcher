import { execSync } from 'node:child_process';
import { request } from '@playwright/test';
import { RequestUtils } from '@wordpress/e2e-test-utils-playwright';
import * as path from 'node:path';
import * as fs from 'node:fs';

const BASE_URL = process.env.WP_BASE_URL ?? 'http://localhost:8889';
const ARTIFACTS_DIR = path.join(__dirname, '..', 'artifacts');
const STORAGE_STATE_DIR =
  process.env.STORAGE_STATE_DIR ?? path.join(ARTIFACTS_DIR, 'storage-states');
const SEED_FILE = path.join(ARTIFACTS_DIR, 'seed.json');

type Subsite = { slug: '' | 'de' | 'it'; title: string; wplang: string };

const SUBSITES: ReadonlyArray<Subsite> = [
  { slug: '', title: 'Root Site', wplang: '' },
  { slug: 'de', title: 'German Site', wplang: 'de_DE' },
  { slug: 'it', title: 'Italian Site', wplang: 'it_IT' },
];

const SEED_POST_TITLES: Record<Subsite['slug'], string> = {
  '': 'MSLS Demo (en)',
  de: 'MSLS Demo (de)',
  it: 'MSLS Demo (it)',
};

const SEED_POST_BODY = '<p>Demo body. Switcher below:</p>\n[sc_msls]';

function wpEnvCli(command: string): string {
  // wp-env prints status lines on stdout (ℹ Starting…, ✔ Ran…). Strip those
  // so callers see only the command output. --quiet is parsed as a positional
  // argument by Docker exec, so we filter post-hoc instead.
  const raw = execSync(`npx wp-env run tests-cli ${command}`, {
    encoding: 'utf8',
    stdio: ['ignore', 'pipe', 'pipe'],
  });
  return raw
    .split('\n')
    .filter((line) => !/^[ℹ✔✖]/.test(line))
    .join('\n')
    .trim();
}

function urlFor(slug: Subsite['slug']): string {
  return slug === '' ? BASE_URL : `${BASE_URL}/${slug}`;
}

function storageStatePath(slug: Subsite['slug']): string {
  const name = slug === '' ? 'admin' : `admin-${slug}`;
  return path.join(STORAGE_STATE_DIR, `${name}.json`);
}

function ensureMultisiteTopology(): void {
  const network = wpEnvCli('wp eval "echo is_multisite() ? \'1\' : \'0\';"');
  if (network !== '1') {
    throw new Error(
      'wp-env tests environment is not multisite. Check .wp-env.json has "multisite": true.'
    );
  }

  // wp site list has no --slug filter and --path is a global flag, so we list
  // all sites once and match by URL substring.
  const existingUrls = wpEnvCli('wp site list --field=url').split('\n');

  for (const { slug, title, wplang } of SUBSITES) {
    if (slug === '') {
      continue;
    }
    const subUrl = `${urlFor(slug)}/`;
    const exists = existingUrls.some((u) => u.trim() === subUrl);

    if (!exists) {
      const created = wpEnvCli(
        `wp site create --slug=${slug} --title="${title}" --porcelain`
      );
      if (!created) {
        throw new Error(`Failed to create subsite "${slug}"`);
      }
    }

    wpEnvCli(`wp option update WPLANG ${wplang} --url=${urlFor(slug)}`);
    wpEnvCli(
      `wp option update permalink_structure '/%postname%/' --url=${urlFor(slug)}`
    );
    // Rewrite rules flush — needed after permalink_structure change.
    wpEnvCli(`wp rewrite flush --hard --url=${urlFor(slug)}`);
  }

  wpEnvCli('wp option update permalink_structure \'/%postname%/\'');
  wpEnvCli('wp rewrite flush --hard');
  wpEnvCli('wp plugin activate multisite-language-switcher --network');
}

function ensureMslsBlogLanguages(): void {
  // The plugin needs each blog to know its language via the msls plugin option.
  for (const { slug, wplang } of SUBSITES) {
    const lang = wplang || 'en_US';
    const url = urlFor(slug);
    // Write the plugin option directly. Values must be strings — MSLS
    // Admin.php only coerces bool→string when rendering checkboxes, so an int
    // 1 trips Checkbox::__construct (typed ?string). See includes/Component
    // /Input/Checkbox.php:28.
    const payload = JSON.stringify({
      blog_language: lang,
      display: '0',
      sort_by_description: '',
      activate_autocomplete: '',
      activate_content_import: '',
      only_with_translation: '',
      output_current_blog: '1',
      content_filter: '1',
      reference_user: '1',
    }).replace(/'/g, "'\"'\"'");
    wpEnvCli(`wp option update msls '${payload}' --format=json --url=${url}`);
  }
}

async function primeStorageStates(): Promise<void> {
  fs.mkdirSync(STORAGE_STATE_DIR, { recursive: true });

  // The same admin user authenticates against every subsite in a multisite
  // install — log in once at the root, then copy that storage state for each
  // subsite. Playwright's request.baseURL path prefix isn't reliably honored
  // by RequestUtils, so we avoid per-subsite REST setup here.
  const rootTarget = storageStatePath('');
  const ctx = await request.newContext({ baseURL: BASE_URL });
  const utils = new RequestUtils(ctx, {
    baseURL: BASE_URL,
    storageStatePath: rootTarget,
  });
  await utils.setupRest();
  await ctx.dispose();

  for (const { slug } of SUBSITES) {
    if (slug === '') continue;
    fs.copyFileSync(rootTarget, storageStatePath(slug));
  }
}

function seedTranslationLinkedPosts(): void {
  type Created = { id: number; slug: string; link: string };
  const created: Record<Subsite['slug'], Created> = {} as Record<
    Subsite['slug'],
    Created
  >;

  // Use wp-cli for post creation — Playwright's request baseURL doesn't
  // reliably carry the multisite path prefix into REST calls, so REST POSTs
  // all land on the root site.
  for (const sub of SUBSITES) {
    const url = urlFor(sub.slug);

    // Delete previously seeded demo posts so reruns are idempotent. wp-env's
    // run wraps Docker exec directly (no shell), so $(...) won't expand —
    // list IDs in JS, then call delete with them as args.
    const idsRaw = wpEnvCli(
      `wp post list --post_type=post --field=ID --format=ids --url=${url}`
    );
    const existing = idsRaw.split(/\s+/).filter(Boolean);
    if (existing.length > 0) {
      wpEnvCli(`wp post delete ${existing.join(' ')} --force --url=${url}`);
    }

    const slugForUrl =
      sub.slug === '' ? 'msls-demo-en' : `msls-demo-${sub.slug}`;
    const titleEsc = SEED_POST_TITLES[sub.slug]
      .replace(/\\/g, '\\\\')
      .replace(/"/g, '\\"');
    const bodyEsc = SEED_POST_BODY.replace(/\\/g, '\\\\').replace(/"/g, '\\"');

    const idRaw = wpEnvCli(
      `wp post create --post_type=post --post_status=publish --post_title="${titleEsc}" --post_name=${slugForUrl} --post_content="${bodyEsc}" --porcelain --url=${url}`
    );
    const id = parseInt(idRaw, 10);
    if (!Number.isFinite(id) || id <= 0) {
      throw new Error(`Failed to create seed post on "${sub.slug || 'root'}" (got: ${idRaw})`);
    }

    created[sub.slug] = {
      id,
      slug: slugForUrl,
      link: `${url}/${slugForUrl}/`,
    };
  }

  // Translation links are stored per-blog as the WP option `msls_<post_id>`
  // — a map of foreign-language codes to the post id on that subsite. See
  // includes/Options/Options.php (PREFIX 'msls', SEPARATOR '_'). REST/meta
  // is not used.
  const linkMap: Record<string, number> = {
    en_US: created[''].id,
    de_DE: created.de.id,
    it_IT: created.it.id,
  };

  for (const sub of SUBSITES) {
    const lang = sub.wplang || 'en_US';
    const others: Record<string, number> = { ...linkMap };
    delete others[lang];

    const postId = created[sub.slug].id;
    const optionName = `msls_${postId}`;
    const payload = JSON.stringify(others).replace(/'/g, "'\"'\"'");

    wpEnvCli(
      `wp option update '${optionName}' '${payload}' --format=json --autoload=no --url=${urlFor(sub.slug)}`
    );
  }

  fs.writeFileSync(
    SEED_FILE,
    JSON.stringify({ posts: created, body: SEED_POST_BODY }, null, 2),
    'utf8'
  );
}

async function globalSetup(): Promise<void> {
  if (process.env.MSLS_SKIP_E2E_SEED === '1') {
    return;
  }

  if (process.env.MSLS_LIVE_ONLY === '1') {
    return;
  }

  ensureMultisiteTopology();
  ensureMslsBlogLanguages();
  await primeStorageStates();
  seedTranslationLinkedPosts();
}

export default globalSetup;
