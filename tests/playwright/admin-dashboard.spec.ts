import { test, expect } from '@playwright/test';

test.describe.configure({ mode: 'parallel' });
test('test edit posts', async ({ page }) => {
  await page.goto('https://msls.co/wp-admin/edit.php');

  const mslscol = await page.locator('#mslscol');
  await expect(mslscol).toHaveAttribute('scope', 'col');
});

test('test edit pages', async ({ page }) => {
  await page.goto('https://msls.co/wp-admin/edit.php?post_type=page');

  const mslscol = await page.locator('#mslscol');
  await expect(mslscol).toHaveAttribute('scope', 'col');
});