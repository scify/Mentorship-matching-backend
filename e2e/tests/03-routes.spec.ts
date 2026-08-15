import { expect, test } from '@playwright/test';
import { assertPageHealthy, login, makeShotter, Role } from './helpers';

const shot = makeShotter('03-routes');

/** Every GET route that renders a page, with the role that may reach it. */
const PUBLIC_ROUTES = [
  '/login',
  '/register',
  '/password/reset',
  '/mentor/create?public=1',
  '/mentee/create?public=1',
];

const ADMIN_ROUTES = [
  '/',
  '/dashboard',
  '/mentors/all',
  '/mentees/all',
  '/users/all',
  '/user/create',
  '/companies/all',
  '/company/create',
  '/reports/all',
  '/sessions/all',
  '/mentor/create',
  '/mentee/create',
];

/** Ajax partials: they return HTML fragments, not full pages. */
const ADMIN_FRAGMENTS = [
  '/mentors/filter',
  '/mentees/filter',
  '/mentors/byCriteria',
  '/sessions/byCriteria',
  '/companies/filter',
  '/users/byCriteria',
  '/search?search_query=a',
];

const ROLE_ROUTES: Array<{ role: Role; path: string }> = [
  { role: 'matcher', path: '/sessions/myMatches' },
  { role: 'accountManager', path: '/sessions/mySessions' },
];

test.describe('every route renders', () => {
  test('public routes', async ({ page }) => {
    for (const path of PUBLIC_ROUTES) {
      const res = await page.goto(path);
      await assertPageHealthy(page, res?.status(), `GET ${path}`);
      await shot(page, `public ${path}`);
    }
  });

  test('admin routes', async ({ page }) => {
    await login(page, 'admin');
    for (const path of ADMIN_ROUTES) {
      const res = await page.goto(path);
      await assertPageHealthy(page, res?.status(), `GET ${path} as admin`);
      expect(res?.status(), `GET ${path} as admin`).toBeLessThan(400);
      await shot(page, `admin ${path}`);
    }
  });

  test('admin ajax fragments', async ({ page }) => {
    await login(page, 'admin');
    for (const path of ADMIN_FRAGMENTS) {
      const res = await page.goto(path);
      await assertPageHealthy(page, res?.status(), `GET ${path} as admin`);
      expect(res?.status(), `GET ${path} as admin`).toBeLessThan(400);
    }
  });

  test('detail pages for a real mentor, mentee and user', async ({ page }) => {
    await login(page, 'admin');

    // Follow the first mentor/mentee row rather than guessing ids.
    await page.goto('/mentors/all');
    const mentorLink = page.locator('a[href*="/profile"]').first();
    if (await mentorLink.count()) {
      const href = await mentorLink.getAttribute('href');
      const res = await page.goto(href!);
      await assertPageHealthy(page, res?.status(), `GET ${href}`);
      await shot(page, 'mentor profile');

      const editHref = href!.replace('/profile', '/edit');
      const editRes = await page.goto(editHref);
      await assertPageHealthy(page, editRes?.status(), `GET ${editHref}`);
      await shot(page, 'mentor edit');
    }

    await page.goto('/mentees/all');
    const menteeLink = page.locator('a[href*="/profile"]').first();
    if (await menteeLink.count()) {
      const href = await menteeLink.getAttribute('href');
      const res = await page.goto(href!);
      await assertPageHealthy(page, res?.status(), `GET ${href}`);
      await shot(page, 'mentee profile');
    }

    await page.goto('/users/all');
    const userLink = page.locator('a[href*="/user/"][href*="/profile"], a[href*="/user/"][href*="/edit"]').first();
    if (await userLink.count()) {
      const href = await userLink.getAttribute('href');
      const res = await page.goto(href!);
      await assertPageHealthy(page, res?.status(), `GET ${href}`);
      await shot(page, 'user detail');
    }
  });

  for (const { role, path } of ROLE_ROUTES) {
    test(`${role} reaches ${path}`, async ({ page }) => {
      await login(page, role);
      const res = await page.goto(path);
      await assertPageHealthy(page, res?.status(), `GET ${path} as ${role}`);
      expect(res?.status(), `GET ${path} as ${role}`).toBeLessThan(400);
      await shot(page, `${role} ${path}`);
    });
  }
});
