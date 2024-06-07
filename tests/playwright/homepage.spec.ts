import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('/');

  await expect(page).toHaveTitle(/Multisite Language Switcher - WordPress multilingual/);

  await page.getByRole('link', { name: 'de_DE' }).first().click();
  await expect(page).toHaveTitle(/Multisite Language Switcher - WordPress mehrsprachig/);

  await page.getByRole('link', { name: 'en_GB' }).first().click();
  await expect(page).toHaveTitle(/Multisite Language Switcher - WordPress multilingual/);
});