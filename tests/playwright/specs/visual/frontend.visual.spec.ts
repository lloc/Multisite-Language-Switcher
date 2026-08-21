import { test, expect } from '../../fixtures/msls-fixtures';

test.describe('MSLS visual — frontend', () => {
  for (const slug of ['', 'de', 'it'] as const) {
    test(`shortcode block on "${slug || 'root'}" post`, async ({ page, seed }) => {
      await page.goto(seed.posts[slug].link);

      const content = page.locator('.entry-content, .wp-block-post-content').first();
      await expect(content).toBeVisible();

      await expect(content).toHaveScreenshot(
        `shortcode-${slug || 'root'}.png`,
        {
          mask: [
            page.locator('time, .wp-block-post-date, .entry-date'),
            page.locator('#wpadminbar'),
          ],
        }
      );
    });

    test(`hreflang head links on "${slug || 'root'}" home`, async ({ page }) => {
      const url = slug === '' ? '/' : `/${slug}/`;
      const response = await page.goto(url);
      const html = (await response?.text()) ?? '';

      // Extract just the hreflang link tags for a textual visual baseline.
      const tags = (html.match(
        /<link[^>]+rel=["']alternate["'][^>]+hreflang=[^>]*>/gi
      ) ?? []).sort();

      expect(tags.join('\n')).toMatchSnapshot(
        `hreflang-${slug || 'root'}.txt`
      );
    });
  }
});
