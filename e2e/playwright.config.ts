import { defineConfig, devices } from '@playwright/test';

/**
 * The suite drives the Dockerised stack (`docker compose up -d`), which nginx
 * exposes on port 89. Override with E2E_BASE_URL to point somewhere else.
 */
export default defineConfig({
  testDir: './tests',
  // Screenshot filenames are numbered per spec, so specs must not interleave.
  fullyParallel: false,
  workers: 1,
  forbidOnly: !!process.env.CI,
  retries: 0,
  reporter: [['list'], ['html', { outputFolder: 'playwright-report', open: 'never' }]],
  timeout: 60_000,
  expect: { timeout: 10_000 },
  use: {
    baseURL: process.env.E2E_BASE_URL ?? 'http://localhost:89',
    viewport: { width: 1440, height: 900 },
    ignoreHTTPSErrors: true,
    trace: 'retain-on-failure',
    screenshot: 'off', // specs capture their own, deliberately named
    actionTimeout: 15_000,
  },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
