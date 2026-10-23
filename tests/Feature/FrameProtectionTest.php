<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Staff pages may only be framed by the app itself (clickjacking). The public
 * forms are embedded in another site, so they stay frameable.
 */
class FrameProtectionTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_staff_pages_refuse_foreign_frames() {
        $response = $this->actingAs($this->makeBackofficeUser(1, 'Admin'))->get('/users/all');

        $response->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'self'");
    }

    public function test_the_public_forms_stay_frameable() {
        $this->get('/mentee/create?public=1')->assertOk()
            ->assertHeaderMissing('X-Frame-Options')
            ->assertHeaderMissing('Content-Security-Policy');
    }
}
