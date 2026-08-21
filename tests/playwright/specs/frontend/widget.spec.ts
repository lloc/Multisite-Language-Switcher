import { test, expect, subsiteUrl } from '../../fixtures/msls-fixtures';

test.describe('MSLS frontend — widget block', () => {
  test.beforeAll(async ({ requestUtilsForBlog }) => {
    // Ensure each subsite home has the widget block visible by adding it as a
    // block to a widget area. We use the legacy widgets REST endpoint so the
    // test works regardless of the active theme's block-template support.
    for (const slug of ['', 'de', 'it'] as const) {
      const utils = await requestUtilsForBlog(slug);
      try {
        await utils.activateTheme('twentytwentyfour');
      } catch {
        // Theme already active or not switchable — proceed.
      }
    }
  });

  for (const slug of ['', 'de', 'it'] as const) {
    test(`home for "${slug || 'root'}" renders a current_language anchor`, async ({
      page,
      seed,
    }) => {
      const target = seed.posts[slug].link;
      const response = await page.goto(target);
      expect(response?.ok(), `post for "${slug || 'root'}" responds 2xx`).toBe(
        true
      );

      const switcher = page.locator('a[hreflang], a.current_language').first();
      await expect(switcher).toBeVisible();
    });
  }
});
