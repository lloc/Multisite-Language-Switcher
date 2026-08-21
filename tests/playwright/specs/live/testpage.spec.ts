import { test, expect } from '@playwright/test';

test.beforeEach(async ({ page }) => {
  await page.goto('/testpage');
});

[
  { selector: '.widget_mslswidget', firstLink: {name: 'de_DE Deutsch'}, secondLink: {name: 'en_GB English'} },
  { selector: '.msls-menu', firstLink: { name: 'de_DE', exact: true }, secondLink: { name: 'en_GB', exact: true } },
].forEach(({ selector, firstLink, secondLink }) => {
  test.describe(() => {
    test(`testing with ${selector} ${firstLink.name} ${secondLink.name}`, async ({page}) => {
      const section = page.locator(selector);

      let element = section.getByRole('link', firstLink).first();
      await element.click();
      await expect(element).toHaveClass(['current_language']);

      element = section.getByRole('link', secondLink).first();
      await element.click();
      await expect(element).toHaveClass(['current_language']);
    })
  })
});

[0, 1, 2].forEach((index) => {
  test.describe(() => {
    test(`testing with link nth(${index})`, async ({ page }) => {
      let section = page.locator('.entry-content');

      let element = section.getByRole('link', { name: 'de_DE Deutsch' }).nth(index) ;
      await element.click();
      await expect(element).toHaveClass(['current_language']);

      element = section.getByRole('link', { name: 'en_GB English' }).nth(index);
      await element.click();
      await expect(element).toHaveClass(['current_language']);
    })
  })
});

test(`testing translation hint`, async ({ page }) => {
  let section = page.locator('.entry-content');

  await section.getByRole('link', { name: 'Deutsch', exact: true }).click();
  await expect(section.getByRole('link', { name: 'English', exact: true })).toHaveCount(0);

  await section.getByRole('link', { name: 'English', exact: true }).click();
  await expect(section.getByRole('link', { name: 'English', exact: true })).toHaveCount(0);
});