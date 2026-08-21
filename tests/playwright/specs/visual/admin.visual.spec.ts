import {
  test,
  expect,
  subsiteUrl,
  storageStatePath,
} from '../../fixtures/msls-fixtures';

const SETTINGS_PATH = '/wp-admin/options-general.php?page=MslsAdmin';

test.describe('MSLS visual — admin', () => {
  test.use({
    storageState: storageStatePath(''),
    baseURL: subsiteUrl(''),
  });

  test('settings page baseline (root subsite)', async ({ page }) => {
    await page.goto(`${subsiteUrl('')}${SETTINGS_PATH}`);

    // The .wrap is the settings form container — masking the subsubsub area
    // keeps the snapshot stable across blog-id reordering.
    const wrap = page.locator('.wrap').first();
    await expect(wrap).toBeVisible();

    await expect(wrap).toHaveScreenshot('settings-root.png', {
      mask: [
        page.locator('#wpadminbar'),
        page.locator('.subsubsub'),
        page.locator('#footer-thankyou, #footer-upgrade'),
      ],
    });
  });

  test('post-list MSLS column with linked translations', async ({ page }) => {
    // The "MSLS" column on edit.php shows flag icons for posts that have
    // translations on other subsites. The Twenty Twenty-Five admin list
    // table is stable enough for a visual baseline.
    await page.goto(`${subsiteUrl('')}/wp-admin/edit.php`);

    const table = page.locator('table.wp-list-table').first();
    await expect(table).toBeVisible();

    await expect(table).toHaveScreenshot('post-list-root.png', {
      mask: [
        page.locator('#wpadminbar'),
        // Mask the Date column — its value updates with every test run.
        page.locator('td.date.column-date'),
      ],
    });
  });
});
