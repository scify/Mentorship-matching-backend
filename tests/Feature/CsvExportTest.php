<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Profile data comes from the public forms. A cell that a spreadsheet would
 * read as a formula is exported as text.
 */
class CsvExportTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_a_formula_from_a_public_form_is_exported_as_text() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        $this->makeMenteeProfile($admin, ['first_name' => '=HYPERLINK("http://evil.test","x")', 'last_name' => '@SUM(1)']);

        $csv = $this->actingAs($admin)->get('/export/mentees')->assertOk()->getContent();

        $this->assertStringContainsString("\"'=HYPERLINK('http://evil.test','x')\"", $csv);
        $this->assertStringContainsString("\"'@SUM(1)\"", $csv);
        $this->assertStringNotContainsString('";"=', $csv);
    }
}
