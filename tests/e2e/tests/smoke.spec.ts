import { test, expect } from '@playwright/test';

// Test 1: Homepage ohne /de
test('Homepage lädt ohne /de', async ({ page }) => {
  const response = await page.goto('https://stage.inspira-zentrum.de/');
  expect(response?.ok()).toBeTruthy();
  await expect(page).toHaveTitle(/Inspira/i);
});

// Test 2: Homepage mit /de
test('Homepage lädt mit /de', async ({ page }) => {
  const response = await page.goto('https://stage.inspira-zentrum.de/de');
  expect(response?.ok()).toBeTruthy();
  await expect(page).toHaveTitle(/Inspira/i);
});

// Test 3: Loginseite
test('Loginseite lädt korrekt', async ({ page }) => {
  await page.goto('https://stage.inspira-zentrum.de/login');
  await expect(page.locator('button[type="submit"]')).toBeVisible();
});
