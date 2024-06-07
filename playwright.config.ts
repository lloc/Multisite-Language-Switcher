import { defineConfig, devices } from '@playwright/test';
import * as dotenv from "dotenv";

dotenv.config({ path: __dirname + '/tests/playwright/.env.local' });

export default defineConfig({
  testDir: './tests/playwright/',
  outputDir: './tests/playwright-results/',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: [
    ['list'],
    ['json', {  outputFile: './tests/playwright-report/test-results.json' }]
  ],
  use: {
    baseURL: 'https://msls.co',
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
      },
    },
  ]
});
