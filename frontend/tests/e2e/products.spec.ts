import { test, expect } from '@playwright/test';


test('renders product list', async ({ page }) => {
await page.goto('http://localhost:3000');
await expect(page.getByRole('heading', { name: 'Products' })).toBeVisible();
await expect(page.locator('li')).toHaveCountGreaterThan(0);
});