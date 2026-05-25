import { test, expect } from '../../fixtures/msls-fixtures';

const SUBSITES = [
  { slug: '' as const, lang: 'en_US', title: 'MSLS smoke (en)' },
  { slug: 'de' as const, lang: 'de_DE', title: 'MSLS smoke (de)' },
  { slug: 'it' as const, lang: 'it_IT', title: 'MSLS smoke (it)' },
];

test.describe('MSLS frontend smoke', () => {
  test.beforeAll(async ({ requestUtilsForBlog }) => {
    for (const { slug } of SUBSITES) {
      const utils = await requestUtilsForBlog(slug);
      await utils.deleteAllPosts();
    }

    const created: Record<string, number> = {};
    for (const { slug, title } of SUBSITES) {
      const utils = await requestUtilsForBlog(slug);
      const post = await utils.createPost({
        title,
        content: `<p>Body for ${title}. [sc_msls]</p>`,
        status: 'publish',
      });
      created[slug || 'root'] = post.id;
    }

    const linkMap: Record<string, number> = {
      en_US: created.root,
      de_DE: created.de,
      it_IT: created.it,
    };

    for (const { slug, lang } of SUBSITES) {
      const utils = await requestUtilsForBlog(slug);
      const save = { ...linkMap };
      delete save[lang];

      await utils.rest({
        method: 'POST',
        path: `/wp/v2/posts/${created[slug || 'root']}`,
        data: { meta: { msls_postmeta: save } },
      });
    }
  });

  test.afterAll(async ({ requestUtilsForBlog }) => {
    for (const { slug } of SUBSITES) {
      const utils = await requestUtilsForBlog(slug);
      await utils.deleteAllPosts();
    }
  });

  test('hreflang alternates are emitted on each subsite home', async ({ page }) => {
    for (const { slug } of SUBSITES) {
      const url = slug === '' ? '/' : `/${slug}/`;
      const response = await page.goto(url);

      expect(response?.ok(), `home for "${slug || 'root'}" should respond 2xx`).toBe(true);

      const html = (await response?.text()) ?? '';
      expect(html).toMatch(/<link[^>]+rel=["']alternate["'][^>]+hreflang=/i);
    }
  });
});
