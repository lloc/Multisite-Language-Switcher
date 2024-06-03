import { test as setup, expect } from '@playwright/test';
import dotenv from 'dotenv';

const authFile = 'tests/playwright/.auth/user.json';

setup('authenticate', async ({ page }) => {
    dotenv.config({ path: 'tests/playwright/.env.local'});

    await page.goto('https://msls.co/wp-login.php?jetpack-sso-show-default-form=1');

    await page.locator('#user_login').fill(process.env.user_login as string);
    await page.locator('#user_pass').fill(process.env.user_pass as string);
    await page.locator('#wp-submit').click();

    await page.context().storageState({ path: authFile });
});