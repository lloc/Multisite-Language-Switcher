import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('https://msls.co/');

  await expect(page).toHaveTitle(/Multisite Language Switcher - WordPress multilingual/);
  await page.getByRole('link', { name: 'de_DE' }).click();

  await expect(page).toHaveTitle(/Multisite Language Switcher - WordPress mehrsprachig/);
  await page.getByRole('link', { name: 'en_GB' }).click();

  await expect(page).toHaveTitle(/Multisite Language Switcher - WordPress multilingual/);
});