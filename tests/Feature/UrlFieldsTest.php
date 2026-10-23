<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Profile LinkedIn URLs and company websites are rendered as links that staff
 * click, so they must be http or https URLs.
 */
class UrlFieldsTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public static function badUrls(): array {
        return [
            'javascript' => ['javascript:alert(1)'],
            'data' => ['data:text/html,<script>alert(1)</script>'],
            'vbscript' => ['vbscript:msgbox(1)'],
        ];
    }

    #[DataProvider('badUrls')]
    public function test_a_staff_edit_refuses_a_non_web_linkedin_url(string $url) {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $mentorId = $this->makeMentorProfile($accountManager, $this->makeCompany($accountManager, 'Company'));
        $menteeId = $this->makeMenteeProfile($accountManager);
        $this->actingAs($accountManager);

        $this->post("/mentor/{$mentorId}/edit", $this->person('mentor') + ['linkedin_url' => $url])->assertSessionHasErrors('linkedin_url');
        $this->post("/mentee/{$menteeId}/edit", $this->person('mentee') + ['linkedin_url' => $url])->assertSessionHasErrors('linkedin_url');
    }

    #[DataProvider('badUrls')]
    public function test_a_company_refuses_a_non_web_website(string $url) {
        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))
            ->post('/company/create', ['name' => 'Acme', 'website' => $url])
            ->assertSessionHasErrors('website');
    }

    #[DataProvider('badUrls')]
    public function test_the_public_forms_refuse_a_non_web_linkedin_url(string $url) {
        $this->post('/mentor/create', ['linkedin_url' => $url, 'public_form' => 'true'])->assertSessionHasErrors('linkedin_url');
        $this->post('/mentee/create', ['linkedin_url' => $url, 'public_form' => 'true'])->assertSessionHasErrors('linkedin_url');
    }

    public function test_an_https_url_is_accepted() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $menteeId = $this->makeMenteeProfile($accountManager);

        $this->actingAs($accountManager)
            ->post("/mentee/{$menteeId}/edit", $this->person('mentee') + ['linkedin_url' => 'https://www.linkedin.com/in/someone'])
            ->assertSessionDoesntHaveErrors('linkedin_url');
    }

    private function person(string $kind): array {
        return [
            'first_name' => 'Edit', 'last_name' => 'Person', 'year_of_birth' => '1990',
            'email' => "edit.{$kind}@example.test", 'public_form' => 'false', 'status_id' => 1,
        ];
    }
}
