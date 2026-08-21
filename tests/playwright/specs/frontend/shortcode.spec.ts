import { test, expect } from '../../fixtures/msls-fixtures';

test.describe('MSLS frontend — [sc_msls] shortcode', () => {
  for (const slug of ['', 'de', 'it'] as const) {
    test(`shortcode on "${slug || 'root'}" post lists the other languages`, async ({
      page,
      seed,
    }) => {
      const response = await page.goto(seed.posts[slug].link);
      expect(response?.ok()).toBe(true);

      // Scope to the switcher's direct parent — .entry-content also contains
      // theme-emitted "Read more" / category links unrelated to MSLS.
      const current = page.locator('a.current_language');
      await expect(current).toHaveCount(1);
      await expect(current).toHaveAttribute('aria-current', 'page');

      const switcher = current.locator('..');
      const switcherAnchors = switcher.locator('> a');
      await expect(switcherAnchors).toHaveCount(3);
    });

    test(`shortcode on "${slug || 'root'}" navigates to another language`, async ({
      page,
      seed,
    }) => {
      await page.goto(seed.posts[slug].link);

      const current = page.locator('a.current_language');
      const switcher = current.locator('..');
      const target = switcher.locator('> a:not(.current_language)').first();
      const href = await target.getAttribute('href');
      expect(href, 'foreign anchor has href').toBeTruthy();

      await Promise.all([page.waitForURL(href!), target.click()]);

      // After navigation, the just-clicked URL's language is now current.
      await expect(page.locator('a.current_language')).toHaveCount(1);
    });
  }
});
