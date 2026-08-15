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

    // Follow the first mentor/mentee row rather than guessing ids. The links
    // must exist — spec 01 registered a mentor and a mentee, and the seed
    // created backoffice users — so a missing link is a failure, not a skip.
    await page.goto('/mentors/all');
    const mentorLink = page.locator('a[href*="/profile"]').first();
    await expect(mentorLink, 'mentors list has no profile link — did spec 01 and `npm run seed` run?').toHaveCount(1);
    const mentorHref = await mentorLink.getAttribute('href');
    const mentorRes = await page.goto(mentorHref!);
    await assertPageHealthy(page, mentorRes?.status(), `GET ${mentorHref}`);
    await shot(page, 'mentor profile');

    const editHref = mentorHref!.replace('/profile', '/edit');
    const editRes = await page.goto(editHref);
    await assertPageHealthy(page, editRes?.status(), `GET ${editHref}`);
    await shot(page, 'mentor edit');

    await page.goto('/mentees/all');
    const menteeLink = page.locator('a[href*="/profile"]').first();
    await expect(menteeLink, 'mentees list has no profile link — did spec 01 and `npm run seed` run?').toHaveCount(1);
    const menteeHref = await menteeLink.getAttribute('href');
    const menteeRes = await page.goto(menteeHref!);
    await assertPageHealthy(page, menteeRes?.status(), `GET ${menteeHref}`);
    await shot(page, 'mentee profile');

    await page.goto('/users/all');
    const userLink = page.locator('a[href*="/user/"][href*="/profile"], a[href*="/user/"][href*="/edit"]').first();
    await expect(userLink, 'users list has no detail link — did `npm run seed` run?').toHaveCount(1);
    const userHref = await userLink.getAttribute('href');
    const userRes = await page.goto(userHref!);
    await assertPageHealthy(page, userRes?.status(), `GET ${userHref}`);
    await shot(page, 'user detail');
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
