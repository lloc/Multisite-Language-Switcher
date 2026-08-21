import { defineConfig, devices } from '@playwright/test';
import * as path from 'node:path';

const isCI = !!process.env.CI;
const LOCAL_BASE_URL = process.env.WP_BASE_URL ?? 'http://localhost:8889';
const LIVE_BASE_URL = process.env.MSLS_LIVE_URL ?? 'https://msls.co';

export default defineConfig({
  testDir: './tests/playwright/specs',
  outputDir: './tests/playwright/artifacts/test-results',
  fullyParallel: true,
  forbidOnly: isCI,
  retries: isCI ? 2 : 0,
  workers: isCI ? 1 : undefined,
  globalSetup: require.resolve('./tests/playwright/setup/global-setup.ts'),
  reporter: [
    ['list'],
    ['json', { outputFile: './tests/playwright/artifacts/test-results.json' }],
    ['html', { outputFolder: './tests/playwright/artifacts/html-report', open: 'never' }],
  ],
  // Pin visual baselines to a single OS — generated inside the Linux Playwright
  // container (npm run playwright:docker) so CI runs are pixel-stable.
  snapshotPathTemplate: '{testDir}/__snapshots__/{testFilePath}/{arg}{ext}',
  use: {
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  expect: {
    toHaveScreenshot: {
      maxDiffPixelRatio: 0.02,
      animations: 'disabled',
      caret: 'hide',
    },
  },
  projects: [
    {
      name: 'local',
      testIgnore: ['**/specs/live/**'],
      use: {
        ...devices['Desktop Chrome'],
        baseURL: LOCAL_BASE_URL,
      },
    },
    {
      name: 'live',
      testMatch: ['**/specs/live/**/*.spec.ts'],
      use: {
        ...devices['Desktop Chrome'],
        baseURL: LIVE_BASE_URL,
      },
    },
  ],
});
