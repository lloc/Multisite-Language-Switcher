import { test as setup } from '@playwright/test';
import dotenv from 'dotenv';

const authFile = 'tests/playwright/.auth/user.json';

setup('authenticate', async ({ request }) => {
    dotenv.config({ path: 'tests/playwright/.env.local' });
    await request.post('https://msls.co/wp-login.php', {
        form: {
            'log': 'user',
            'pwd': 'password'
        }
    });
    await request.storageState({ path: authFile });
});