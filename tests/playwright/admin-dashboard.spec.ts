import { loginUser } from '@wordpress/e2e-test-utils';
import { test, expect } from '@playwright/test';

test.describe.configure({ mode: 'parallel' });

test.beforeEach(async ({page}) => {
  page.goto('/wp-login.php?jetpack-sso-show-default-form=1' );

  await loginUser(process.env.WP_USERNAME, process.env.WP_PASSWORD);
});

test('test edit posts', async ({ page }) => {
  page.goto('/wp-admin/edit.php' );

  const mslscol = await page.locator('#mslscol');
  await expect(mslscol).toHaveAttribute('scope', 'col');
});

test('test edit pages', async ({ page }) => {
  page.goto('/wp-admin/edit.php?post_type=page' );

  const mslscol = await page.locator('#mslscol');
  await expect(mslscol).toHaveAttribute('scope', 'col');
});