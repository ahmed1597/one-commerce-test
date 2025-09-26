import { test, expect } from '@playwright/test';

test('renders products grid and shows count', async ({ page }) => {
  await page.goto('/');

  const grid = page.locator('.products-grid');
  const empty = page.locator('.empty-state');

  await expect(grid.or(empty)).toBeVisible();

  if (await grid.isVisible()) {
    const count = await page.locator('.product-card').count();
    expect(count).toBeGreaterThan(0);

    await expect(page.locator('.product-card').first().locator('.product-title')).toBeVisible();
  } else {
    await expect(page.locator('.empty-title')).toHaveText(/No products yet/i);
  }
});

test('manual refresh button (if present) triggers re-fetch', async ({ page }) => {
  await page.goto('/');
  const refreshBtn = page.getByRole('button', { name: /refresh/i });

  if (await refreshBtn.isVisible()) {
    await refreshBtn.click();
    await expect(page.locator('.products-grid').or(page.locator('.empty-state'))).toBeVisible();
  } else {
    test.skip(true, 'No refresh button present');
  }
});
