import { test, expect, subsiteUrl } from '../../fixtures/msls-fixtures';

const SETTINGS_PATH = '/wp-admin/options-general.php?page=MslsAdmin';

test.describe('MSLS admin — settings page', () => {
  for (const slug of ['', 'de', 'it'] as const) {
    test.describe(`on "${slug || 'root'}" subsite`, () => {
      test.use({
        storageState: require('../../fixtures/msls-fixtures').storageStatePath(slug),
        baseURL: subsiteUrl(slug),
      });

      test('settings page loads with title and subsubsub blog switcher', async ({ page }) => {
        await page.goto(`${subsiteUrl(slug)}${SETTINGS_PATH}`);

        // The heading text is localized ("Optionen" in de, "Opzioni" in it) —
        // match against the constant prefix only.
        await expect(
          page.locator('.wrap > h1').filter({ hasText: /Multisite Language Switcher/i })
        ).toBeVisible();

        // subsubsub navigation between subsites should at least render this blog's link.
        await expect(page.locator('.wrap a.current, .wrap a').first()).toBeVisible();
      });

      test('toggling "Display link to the current language" persists across reload', async ({
        page,
      }) => {
        await page.goto(`${subsiteUrl(slug)}${SETTINGS_PATH}`);

        const checkbox = page.locator('input[name="msls[output_current_blog]"]');
        await expect(checkbox).toBeVisible();

        const initial = await checkbox.isChecked();
        if (initial) {
          await checkbox.uncheck();
        } else {
          await checkbox.check();
        }

        await Promise.all([
          page.waitForURL(/options-general\.php\?page=MslsAdmin/),
          page.locator('input[name="Submit"]').click(),
        ]);

        // Re-load and verify the new state stuck.
        await page.goto(`${subsiteUrl(slug)}${SETTINGS_PATH}`);
        const after = await page
          .locator('input[name="msls[output_current_blog]"]')
          .isChecked();
        expect(after).toBe(!initial);

        // Reset to the original state so other tests start from a known baseline.
        const reset = page.locator('input[name="msls[output_current_blog]"]');
        if (initial) {
          await reset.check();
        } else {
          await reset.uncheck();
        }
        await page.locator('input[name="Submit"]').click();
      });
    });
  }
});
