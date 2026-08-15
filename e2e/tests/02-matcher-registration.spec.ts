import { expect, test } from '@playwright/test';
import { assertPageHealthy, login, logout, makeShotter, RUN_ID, setMultiSelect } from './helpers';

const shot = makeShotter('02-matcher');

const MATCHER_EMAIL = `e2e.matcher.${RUN_ID}@example.test`;
const MATCHER_PASSWORD = 'password123';

test.describe('backoffice user registration', () => {
  test('an admin registers a Matcher, who can then log in and reach their matches', async ({ page }) => {
    await login(page, 'admin');
    await shot(page, 'admin dashboard');

    const res = await page.goto('/user/create');
    await assertPageHealthy(page, res?.status(), 'GET /user/create');
    await shot(page, 'create user form empty');

    await page.fill('input[name="first_name"]', 'Maria');
    await page.fill('input[name="last_name"]', `Matcher ${RUN_ID}`);
    await page.fill('input[name="email"]', MATCHER_EMAIL);
    await page.fill('input[name="password"]', MATCHER_PASSWORD);
    await page.fill('input[name="passwordconfirm"]', MATCHER_PASSWORD);

    // role id 2 = Matcher (UserAccessManager::MATCHER_ROLE_ID)
    await setMultiSelect(page, 'user_roles[][id]', ['2']);

    const icon = page.locator('input[name="usericon"]').first();
    if (await icon.count()) await icon.check();
    await shot(page, 'create user form filled');

    await page.click('button[type="submit"], input[type="submit"]');
    await page.waitForLoadState('networkidle');
    await shot(page, 'user created');

    // The new matcher must show up in the users list.
    const listRes = await page.goto('/users/all');
    await assertPageHealthy(page, listRes?.status(), 'GET /users/all');
    await expect(page.locator('body')).toContainText(MATCHER_EMAIL);
    await shot(page, 'users list with new matcher');

    // And must actually be able to authenticate with the Matcher role.
    await logout(page);
    await page.goto('/login');
    await page.fill('input[name="email_address"]', MATCHER_EMAIL);
    await page.fill('input[name="password"]', MATCHER_PASSWORD);
    await Promise.all([
      page.waitForURL((u) => !u.pathname.endsWith('/login'), { timeout: 20_000 }),
      page.click('button[type="submit"], input[type="submit"]'),
    ]);
    await expect(page.locator('#logout-form')).toHaveCount(1);
    await shot(page, 'new matcher logged in');

    // sessions/myMatches is gated by the can-create-mentorship-session
    // middleware, so reaching it proves the role took effect.
    const matchesRes = await page.goto('/sessions/myMatches');
    await assertPageHealthy(page, matchesRes?.status(), 'GET /sessions/myMatches as matcher');
    expect(matchesRes?.status()).toBeLessThan(400);
    await shot(page, 'matcher my matches');
  });
});
