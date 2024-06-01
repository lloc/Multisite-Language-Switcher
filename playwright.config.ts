import { defineConfig, devices } from '@playwright/test';

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
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
