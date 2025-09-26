import { defineConfig } from '@playwright/test';
export default defineConfig({
webServer: { command: 'npx nuxt dev', port: 3000, timeout: 120000, reuseExistingServer: !process.env.CI },
use: { headless: true }
});