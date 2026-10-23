<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * The version is defined only in package.json (README "Releasing"); staff see
 * it at the bottom of the sidebar, guests do not.
 */
class AppVersionTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_the_app_version_comes_from_package_json() {
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $this->packageVersion());
        $this->assertSame($this->packageVersion(), config('app.version'));
    }

    public function test_the_staff_sidebar_shows_the_app_version() {
        $this->actingAs($this->makeBackofficeUser(2, 'Matcher'))
            ->get('/')
            ->assertOk()
            ->assertSee('version ' . $this->packageVersion());
    }

    public function test_the_login_page_does_not_show_the_app_version() {
        $this->get('/login')->assertOk()->assertDontSee('version ' . $this->packageVersion());
    }

    private function packageVersion(): string {
        return json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR)['version'] ?? '';
    }
}
