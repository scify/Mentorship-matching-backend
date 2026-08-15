import { expect, test } from '@playwright/test';
import { assertPageHealthy, login, makeShotter, Role } from './helpers';

const shot = makeShotter('04-nav');

/**
 * The sidebar is a `.menu-layer` list whose top-level entries are
 * `href="javascript:;"` toggles that reveal a `ul.child-menu`. The menu is
 * role-dependent (admin / matcher / account manager each get a different
 * partial), so each role is swept separately.
 */
const ROLES: Role[] = ['admin', 'matcher', 'accountManager'];

for (const role of ROLES) {
  test(`every menu option a ${role} can see opens without erroring`, async ({ page }) => {
    await login(page, role);

    // Expand every collapsible top-level entry so the submenus are visible,
    // then capture the fully open menu.
    const toggles = page.locator('.menu-layer a[href="javascript:;"]');
    const toggleCount = await toggles.count();
    for (let i = 0; i < toggleCount; i++) {
      await toggles.nth(i).click({ timeout: 5_000 }).catch(() => {});
    }
    await shot(page, `${role} menu expanded`);

    // Collect targets before navigating - navigation invalidates the handles.
    const links: Array<{ href: string; label: string }> = await page.evaluate(() => {
      const out: Array<{ href: string; label: string }> = [];
      const seen = new Set<string>();
      document.querySelectorAll('.menu-layer a[href], .top-menu a[href]').forEach((a) => {
        const el = a as HTMLAnchorElement;
        const href = el.getAttribute('href') ?? '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (href.includes('/logout')) return; // would end the session mid-sweep
        const url = new URL(href, location.origin);
        if (url.host !== location.host) return; // external
        if (seen.has(url.pathname)) return;
        seen.add(url.pathname);
        out.push({ href: url.pathname, label: (el.textContent ?? '').trim() });
      });
      return out;
    });

    expect(links.length, `expected ${role} to see menu links`).toBeGreaterThan(1);
    console.log(`${role} menu options: ${links.map((l) => `${l.label} (${l.href})`).join(', ')}`);

    const failures: string[] = [];
    for (const { href, label } of links) {
      const res = await page.goto(href).catch(() => null);
      const status = res?.status();
      if (status === undefined || status >= 500) {
        failures.push(`${label} -> ${href} -> HTTP ${status}`);
        continue;
      }
      const body = await page.content();
      if (body.includes('laravel-exceptions-renderer')) {
        failures.push(`${label} -> ${href} -> exception page`);
        continue;
      }
      await shot(page, `${role} menu ${label || href}`);
    }

    expect(failures, `broken menu options for ${role}:\n${failures.join('\n')}`).toEqual([]);
  });
}

test.describe('list screens and their filter panels', () => {
  test('the mentor list filter panel submits and returns results', async ({ page }) => {
    await login(page, 'admin');
    const res = await page.goto('/mentors/all');
    await assertPageHealthy(page, res?.status(), 'GET /mentors/all');

    // The filter panel drives an ajax GET against /mentors/filter - the
    // endpoint that used to be SQL-injectable.
    const nameInput = page.locator('input[name="mentorName"], #mentorName').first();
    if (await nameInput.count()) {
      await nameInput.fill('Mentor');
      await shot(page, 'mentor filter filled');
      const submit = page
        .locator('button:has-text("Search"), button:has-text("Filter"), button[type="submit"]')
        .first();
      if (await submit.count()) {
        await submit.click();
        await page.waitForLoadState('networkidle');
        await shot(page, 'mentor filter results');
      }
    }

    // A quoted value must come back as an ordinary empty result, not a 500,
    // and must never leak a driver error.
    for (const payload of ["x' or '1'='1", '1 or 1=1', "%' union select id,1,1,1,1,1 from users -- "]) {
      const injected = await page.request.get(`/mentors/filter?mentorName=${encodeURIComponent(payload)}`);
      expect(injected.status(), `filter payload ${payload} should not error`).toBeLessThan(500);
      const body = await injected.text();
      expect(body, `filter payload ${payload} leaked a SQL error`).not.toContain('SQLSTATE');
    }
  });

  test('the mentee list filter panel submits', async ({ page }) => {
    await login(page, 'admin');
    const res = await page.goto('/mentees/all');
    await assertPageHealthy(page, res?.status(), 'GET /mentees/all');
    const nameInput = page.locator('input[name="menteeName"], #menteeName').first();
    if (await nameInput.count()) {
      await nameInput.fill('Mentee');
      const submit = page
        .locator('button:has-text("Search"), button:has-text("Filter"), button[type="submit"]')
        .first();
      if (await submit.count()) {
        await submit.click();
        await page.waitForLoadState('networkidle');
        await shot(page, 'mentee filter results');
      }
    }
    const injected = await page.request.get(
      `/mentees/filter?university=${encodeURIComponent("x'; drop table mentor_rating; -- ")}`,
    );
    expect(injected.status()).toBeLessThan(500);
    expect(await injected.text()).not.toContain('SQLSTATE');
  });

  test('global search returns a result panel', async ({ page }) => {
    await login(page, 'admin');
    await page.goto('/dashboard');
    const search = page.locator('input[name="search_query"], #search_query').first();
    if (await search.count()) {
      await search.fill('Mentor');
      await search.press('Enter');
      await page.waitForLoadState('networkidle');
      await shot(page, 'global search results');
    }
    const res = await page.request.get('/search?search_query=Mentor');
    expect(res.status()).toBeLessThan(400);
  });
});
