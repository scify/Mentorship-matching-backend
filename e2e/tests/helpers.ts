import { expect, Page } from '@playwright/test';
import fs from 'fs';
import path from 'path';

export const SHOTS_DIR = path.join(__dirname, '..', 'screenshots');

export const USERS = {
  admin: { email: 'admin@jobpairs.test', password: 'password123', label: 'Administrator' },
  matcher: { email: 'matcher@jobpairs.test', password: 'password123', label: 'Matcher' },
  accountManager: { email: 'accman@jobpairs.test', password: 'password123', label: 'Account Manager' },
} as const;

export type Role = keyof typeof USERS;

/** Unique-per-run suffix so re-runs don't trip the unique email constraints. */
export const RUN_ID = process.env.E2E_RUN_ID ?? String(Date.now()).slice(-7);

/**
 * Screenshots are numbered per spec file so the contact sheet reads as a
 * walkthrough rather than an alphabetical jumble. Each spec creates its own
 * shotter with a fixed group prefix; worker reuse can't bleed counters
 * between files that way.
 */
export function makeShotter(group: string) {
  let index = 0;
  return async function shot(page: Page, name: string) {
    fs.mkdirSync(SHOTS_DIR, { recursive: true });
    index += 1;
    const slug = name.replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '').toLowerCase();
    const base = `${group}-${String(index).padStart(2, '0')}-${slug}.png`;
    const file = path.join(SHOTS_DIR, base);
    // Let async widgets (Select2, DataTables, Chosen) settle before capturing.
    await page.waitForLoadState('networkidle').catch(() => {});
    await page.screenshot({ path: file, fullPage: true });
    // Record the human-readable label and the URL actually captured, so
    // reports don't have to reverse-engineer them out of the filename.
    fs.appendFileSync(
      path.join(SHOTS_DIR, 'manifest.jsonl'),
      JSON.stringify({ file: base, group, index, label: name, url: page.url() }) + '\n',
    );
    return file;
  };
}

export async function login(page: Page, role: Role = 'admin') {
  const user = USERS[role];
  await page.goto('/login');
  await page.fill('input[name="email_address"]', user.email);
  await page.fill('input[name="password"]', user.password);
  await Promise.all([
    page.waitForURL((u) => !u.pathname.endsWith('/login'), { timeout: 20_000 }),
    page.click('button[type="submit"], input[type="submit"]'),
  ]);
  await expect(page.locator('#logout-form')).toHaveCount(1, { timeout: 10_000 });
}

export async function logout(page: Page) {
  const form = page.locator('#logout-form').first();
  if (await form.count()) {
    await form.evaluate((f: HTMLFormElement) => f.submit());
    await page.waitForURL(/\/login$/, { timeout: 20_000 }).catch(() => {});
  }
}

/**
 * A page is "broken" if the server 5xx'd or Laravel rendered its exception
 * page. Checking only the status code misses errors swallowed into a 200.
 */
export async function assertPageHealthy(page: Page, status: number | undefined, where: string) {
  expect(status, `${where} returned HTTP ${status}`).toBeLessThan(500);
  const body = await page.content();
  const markers = [
    'laravel-exceptions-renderer',
    'Whoops, looks like something went wrong',
    'Fatal error',
    'Parse error',
    'Uncaught Error',
  ];
  for (const marker of markers) {
    expect(body.includes(marker), `${where} rendered an error page (matched "${marker}")`).toBe(false);
  }
}

/**
 * Select2/Chosen replace <select> with their own widget, so drive the
 * underlying element directly. Pass '__first__' to take the first real
 * option when ids are not stable across environments.
 */
export async function setSelect(page: Page, name: string, value: string) {
  await page.evaluate(
    ([n, v]) => {
      const el = document.querySelector(`select[name="${n}"]`) as HTMLSelectElement | null;
      if (!el) throw new Error(`select[name="${n}"] not found`);
      const real = Array.from(el.options).filter((o) => o.value !== '');
      if (!real.length) throw new Error(`select[name="${n}"] has no selectable options`);
      const opt = v === '__first__' ? real[0] : (real.find((o) => o.value === v) ?? real[0]);
      el.value = opt.value;
      el.dispatchEvent(new Event('change', { bubbles: true }));
    },
    [name, value] as const,
  );
}

/**
 * Laravel re-renders the form with inline errors rather than redirecting, so
 * a failed submit otherwise looks like "the page just didn't change".
 */
export async function validationErrors(page: Page): Promise<string[]> {
  return page.evaluate(() =>
    Array.from(
      document.querySelectorAll('.help-block, .alert-danger, .text-danger, .invalid-feedback, span.error'),
    )
      .map((el) => (el.textContent ?? '').trim())
      .filter((t) => t.length > 0 && t.length < 300),
  );
}

/**
 * iCheck hides the real checkbox behind an <ins class="iCheck-helper"> overlay
 * that swallows pointer events, so a normal .check() never lands. Set the
 * property and fire the events the page listens for instead.
 */
export async function setCheckbox(page: Page, name: string, checked = true) {
  await page.evaluate(
    ([n, c]) => {
      const el = document.querySelector(`input[name="${n}"]`) as HTMLInputElement | null;
      if (!el) throw new Error(`input[name="${n}"] not found`);
      el.checked = c as boolean;
      el.dispatchEvent(new Event('change', { bubbles: true }));
      el.dispatchEvent(new Event('ifChanged', { bubbles: true })); // iCheck
    },
    [name, checked] as const,
  );
}

/** Multi-selects (specialties[][id], industries[][id], user_roles[][id]). */
export async function setMultiSelect(page: Page, name: string, values: string[]) {
  await page.evaluate(
    ([n, vs]) => {
      const el = document.querySelector(`select[name="${n}"]`) as HTMLSelectElement | null;
      if (!el) throw new Error(`select[name="${n}"] not found`);
      Array.from(el.options).forEach((o) => (o.selected = false));
      const real = Array.from(el.options).filter((o) => o.value !== '');
      const wanted = vs.length ? vs : [real[0]?.value].filter(Boolean);
      wanted.forEach((v) => {
        const opt = Array.from(el.options).find((o) => o.value === v);
        if (opt) opt.selected = true;
      });
      el.dispatchEvent(new Event('change', { bubbles: true }));
    },
    [name, values] as const,
  );
}
