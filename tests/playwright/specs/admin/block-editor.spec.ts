import {
  test,
  expect,
  subsiteUrl,
  storageStatePath,
} from '../../fixtures/msls-fixtures';

test.describe('MSLS admin — Gutenberg block', () => {
  test.use({
    storageState: storageStatePath(''),
    baseURL: subsiteUrl(''),
  });

  test('MSLS widget block can be inserted into a new post', async ({ page }) => {
    await page.goto(`${subsiteUrl('')}/wp-admin/post-new.php`);

    // Dismiss any onboarding overlays.
    const welcomeClose = page.getByRole('button', { name: /close|Welcome Guide/i });
    await welcomeClose.first().click({ timeout: 1500 }).catch(() => {});

    // Open the block inserter via keyboard shortcut, search and pick the block.
    await page.keyboard.press('Control+Alt+y').catch(() => {});
    const toggleInserter = page.getByRole('button', { name: /Toggle block inserter|Block Inserter/ });
    await toggleInserter.first().click().catch(() => {});

    const search = page.getByPlaceholder(/Search/);
    await search.first().fill('Multisite Language Switcher');

    const blockOption = page.getByRole('option', { name: /Multisite Language Switcher/ });
    await expect(blockOption.first()).toBeVisible({ timeout: 10_000 });
    await blockOption.first().click();

    // Gutenberg renders the canvas inside an editor iframe in WP 6.x+. The
    // block breadcrumb at the bottom of the page is the most reliable host-
    // side signal that insertion succeeded.
    await expect(
      page
        .getByRole('list', { name: /Block breadcrumb/i })
        .getByText(/Multisite Language Switcher/)
        .first()
    ).toBeVisible({ timeout: 10_000 });
  });
});
