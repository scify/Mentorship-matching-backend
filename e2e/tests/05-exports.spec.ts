import { test, expect } from '@playwright/test';
import { login } from './helpers';

/**
 * The CSV export endpoints are the only admin routes that don't render a
 * page, so the route-walking specs never touch them. They run hand-written
 * SQL through DB::select(), which the Laravel 8 -> 13 upgrade changed the
 * contract of — so they get their own spec.
 */
test.describe('CSV exports', () => {
  const EXPORTS = [
    { path: '/export/mentors', name: 'mentors' },
    { path: '/export/mentees', name: 'mentees' },
    { path: '/export/sessions', name: 'sessions' },
  ];

  for (const { path, name } of EXPORTS) {
    test(`${name} export downloads a CSV without erroring`, async ({ page }) => {
      await login(page, 'admin');
      const response = await page.request.get(path);

      expect(response.status(), `${path} returned HTTP ${response.status()}`).toBe(200);

      const body = await response.text();
      // A 200 that carries Laravel's exception page or a DB error is still broken.
      for (const marker of ['laravel-exceptions-renderer', 'SQLSTATE', 'Whoops, looks like something went wrong']) {
        expect(body.includes(marker), `${path} leaked "${marker}"`).toBe(false);
      }

      if (body === 'NO_DATA_FOUND') {
        // Legitimate empty-table response (e.g. no sessions seeded yet).
        return;
      }
      expect(response.headers()['content-type'] ?? '').toContain('csv');
      expect(response.headers()['content-disposition'] ?? '').toContain('attachment');
      // First line must look like a CSV header row (the exporters use "," or ";").
      expect(body.split('\n')[0]).toMatch(/[,;]/);
    });
  }
});
