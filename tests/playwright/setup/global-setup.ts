import { execSync } from 'node:child_process';
import { request } from '@playwright/test';
import { RequestUtils } from '@wordpress/e2e-test-utils-playwright';
import * as path from 'node:path';
import * as fs from 'node:fs';

const BASE_URL = process.env.WP_BASE_URL ?? 'http://localhost:8889';
const STORAGE_STATE_PATH =
  process.env.STORAGE_STATE_PATH ??
  path.join(__dirname, '..', 'artifacts', 'storage-states', 'admin.json');

const SUBSITES: ReadonlyArray<{ slug: string; title: string; wplang: string }> = [
  { slug: 'de', title: 'German Site', wplang: 'de_DE' },
  { slug: 'it', title: 'Italian Site', wplang: 'it_IT' },
];

function wpEnvCli(command: string): string {
  return execSync(`npx wp-env run tests-cli --quiet -- ${command}`, {
    encoding: 'utf8',
    stdio: ['ignore', 'pipe', 'pipe'],
  }).trim();
}

function ensureMultisiteTopology(): void {
  const network = wpEnvCli('wp eval "echo is_multisite() ? \'1\' : \'0\';"');
  if (network !== '1') {
    throw new Error(
      'wp-env tests environment is not multisite. Check .wp-env.json has "multisite": true.'
    );
  }

  for (const { slug, title, wplang } of SUBSITES) {
    const existing = wpEnvCli(
      `wp site list --slug=${slug} --field=blog_id --format=ids`
    );

    let blogId = existing;
    if (!blogId) {
      blogId = wpEnvCli(
        `wp site create --slug=${slug} --title="${title}" --porcelain`
      );
    }

    if (!blogId) {
      throw new Error(`Failed to create or locate subsite "${slug}"`);
    }

    wpEnvCli(`wp option update WPLANG ${wplang} --url=${BASE_URL}/${slug}`);
  }

  wpEnvCli('wp option update WPLANG ""');
  wpEnvCli('wp plugin activate multisite-language-switcher --network');
}

async function primeStorageState(): Promise<void> {
  fs.mkdirSync(path.dirname(STORAGE_STATE_PATH), { recursive: true });

  const requestContext = await request.newContext({ baseURL: BASE_URL });
  const requestUtils = new RequestUtils(requestContext, {
    baseURL: BASE_URL,
    storageStatePath: STORAGE_STATE_PATH,
  });

  await requestUtils.setupRest();
  await requestContext.dispose();
}

async function globalSetup(): Promise<void> {
  if (process.env.MSLS_SKIP_E2E_SEED === '1') {
    return;
  }

  if (process.env.MSLS_LIVE_ONLY === '1') {
    return;
  }

  ensureMultisiteTopology();
  await primeStorageState();
}

export default globalSetup;
