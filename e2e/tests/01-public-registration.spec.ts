import { expect, test } from '@playwright/test';
import {
  assertPageHealthy,
  makeShotter,
  RUN_ID,
  setCheckbox,
  setMultiSelect,
  setSelect,
  validationErrors,
} from './helpers';

const shot = makeShotter('01-register');

export const MENTOR_EMAIL = `e2e.mentor.${RUN_ID}@example.test`;
export const MENTEE_EMAIL = `e2e.mentee.${RUN_ID}@example.test`;

test.describe('public registration forms', () => {
  test('registers a mentor through the public form', async ({ page }) => {
    const res = await page.goto('/mentor/create?public=1');
    await assertPageHealthy(page, res?.status(), 'GET /mentor/create');
    await shot(page, 'mentor form empty');

    await page.fill('input[name="first_name"]', 'Marina');
    await page.fill('input[name="last_name"]', `Mentor ${RUN_ID}`);
    await page.fill('input[name="email"]', MENTOR_EMAIL);
    await page.fill('input[name="year_of_birth"]', '1985');
    await page.fill('input[name="address"]', '12 Ermou Street');
    await page.fill('input[name="phone"]', '2100000000');
    await page.fill('input[name="cell_phone"]', '6900000000');
    await page.fill('input[name="company_sector"]', 'Technology');
    await page.fill('input[name="job_position"]', 'Engineering Manager');
    await page.fill('input[name="job_experience_years"]', '12');
    await page.fill('textarea[name="skills"]', 'Mentoring, team leadership, PHP, SQL');

    await setSelect(page, 'residence_id', '1');
    await setSelect(page, 'education_level_id', '1');
    await setSelect(page, 'university_id', '1');
    await setSelect(page, 'reference_id', '1');
    // company_id has no fixed id across environments - take whatever is first
    await setSelect(page, 'company_id', '__first__');
    await setMultiSelect(page, 'specialties[][id]', []);
    await setMultiSelect(page, 'industries[][id]', []);

    await setCheckbox(page, 'terms');
    await shot(page, 'mentor form filled');

    await page.click('button[type="submit"], input[type="submit"]');
    await page.waitForLoadState('networkidle');
    await shot(page, 'mentor submitted');

    // The public form redirects back to itself with a flash message.
    const errors = await validationErrors(page);
    const body = await page.locator('body').innerText();
    expect(
      body.includes('Ευχαριστ') || /success/i.test(body) || body.includes('θα επικοινωνήσ'),
      `mentor form was rejected. Validation errors: ${JSON.stringify(errors, null, 2)}`,
    ).toBe(true);
  });

  test('registers a mentee through the public form', async ({ page }) => {
    const res = await page.goto('/mentee/create?public=1');
    await assertPageHealthy(page, res?.status(), 'GET /mentee/create');
    await shot(page, 'mentee form empty');

    await page.fill('input[name="first_name"]', 'Nikos');
    await page.fill('input[name="last_name"]', `Mentee ${RUN_ID}`);
    await page.fill('input[name="email"]', MENTEE_EMAIL);
    await page.fill('input[name="year_of_birth"]', '1999');
    await page.fill('input[name="address"]', '5 Solonos Street');

    // Field set differs slightly from the mentor form; fill what exists.
    for (const [selector, value] of [
      ['input[name="phone"]', '2100000001'],
      ['input[name="cell_phone"]', '6900000001'],
      ['input[name="university_department_name"]', 'Computer Science'],
      ['input[name="university_graduation_year"]', '2021'],
    ] as const) {
      const el = page.locator(selector);
      if (await el.count()) await el.first().fill(value);
    }
    for (const [selector, value] of [
      ['textarea[name="specialty_experience"]', 'Two internships in software engineering.'],
      ['textarea[name="expectations"]', 'Guidance on choosing a specialisation.'],
      ['textarea[name="career_goals"]', 'Become a backend engineer.'],
      ['textarea[name="skills"]', 'PHP, JavaScript, SQL'],
      ['textarea[name="job_description"]', 'Junior developer'],
    ] as const) {
      const el = page.locator(selector);
      if (await el.count()) await el.first().fill(value);
    }

    for (const name of ['residence_id', 'education_level_id', 'university_id', 'reference_id']) {
      if (await page.locator(`select[name="${name}"]`).count()) {
        await setSelect(page, name, '__first__');
      }
    }
    if (await page.locator('select[name="specialties[][id]"]').count()) {
      await setMultiSelect(page, 'specialties[][id]', []);
    }

    if (await page.locator('input[name="terms"]').count()) await setCheckbox(page, 'terms');
    await shot(page, 'mentee form filled');

    await page.click('button[type="submit"], input[type="submit"]');
    await page.waitForLoadState('networkidle');
    await shot(page, 'mentee submitted');

    const errors = await validationErrors(page);
    const body = await page.locator('body').innerText();
    expect(
      body.includes('Ευχαριστ') || /success/i.test(body) || body.includes('θα επικοινωνήσ'),
      `mentee form was rejected. Validation errors: ${JSON.stringify(errors, null, 2)}`,
    ).toBe(true);
  });
});
