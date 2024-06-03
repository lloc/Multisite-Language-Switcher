import { test } from '@playwright/test';
import * as fs from 'fs';
import * as dotenv from 'dotenv';

dotenv.config({
    path: './tests/playwright/.env.local',
});

const storageState = './tests/playwright-results/.auth/storageState.json';

test('authenticate user', async ({ page, context, contextOptions, playwright }) => {
    if (process.env.username === '**REMOVED**') {
        throw new Error('Env file is not correct');
    }

    const stats = fs.existsSync(storageState!.toString()) ? fs.statSync(storageState!.toString()) : null;
    if (stats && stats.mtimeMs > new Date().getTime() - 600000) {
        console.log(`\x1b[2m\tSign in skipped because token is fresh\x1b[0m`);
        return;
    }

    console.log(`\x1b[2m\tSign in started'\x1b[0m`);

    // when we're not authenticated, the app redirects to the login page
    await page.goto('');

    console.log(`\x1b[2m\tSign in as '${process.env.username}'\x1b[0m`);

    await page.getByRole('textbox', { name: /username/i }).fill(process.env.username as string);
    await page.getByLabel('Password').fill(process.env.password as string);

    console.log(`\x1b[2m\tSign in processing\x1b[0m`);

    await page.getByRole('button', { name: /submit/i }).click();

    console.log(`\x1b[2m\tSign in processed\x1b[0m`);

    await page.context().storageState({ path: storageState });
});