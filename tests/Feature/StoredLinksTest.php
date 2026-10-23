<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * Rows saved before the URL rules may hold a javascript: link. The profile and
 * company pages render a link only for http and https URLs.
 */
class StoredLinksTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const BAD = 'javascript:alert(1)';

    public function test_profiles_do_not_link_a_stored_javascript_url() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        $mentorId = $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company'), ['linkedin_url' => self::BAD]);
        $menteeId = $this->makeMenteeProfile($admin, ['linkedin_url' => self::BAD]);
        $this->actingAs($admin);

        $this->get("/mentor/{$mentorId}/profile")->assertOk()->assertDontSee('href="' . self::BAD, false)->assertSee(self::BAD);
        $this->get("/mentee/{$menteeId}/profile")->assertOk()->assertDontSee('href="' . self::BAD, false)->assertSee(self::BAD);
    }

    public function test_the_company_list_does_not_link_a_stored_javascript_url() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        DB::table('company')->where('id', $this->makeCompany($admin, 'Company'))->update(['website' => self::BAD]);

        $this->actingAs($admin)->get('/companies/all')->assertOk()->assertDontSee('href="' . self::BAD, false);
    }

    public function test_an_https_url_is_still_a_link() {
        $admin = $this->makeBackofficeUser(1, 'Admin');
        $menteeId = $this->makeMenteeProfile($admin, ['linkedin_url' => 'https://www.linkedin.com/in/someone']);

        $this->actingAs($admin)->get("/mentee/{$menteeId}/profile")->assertSee('href="https://www.linkedin.com/in/someone"', false);
    }
}
