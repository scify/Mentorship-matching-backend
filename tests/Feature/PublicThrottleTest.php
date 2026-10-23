<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

/**
 * The public routes are throttled per client, so a script cannot flood the
 * registration forms, guess email-link ids or send reset mails in a loop.
 */
class PublicThrottleTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        Notification::fake();
    }

    public function test_the_public_forms_accept_five_posts_in_ten_minutes() {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/mentee/create', [])->assertStatus(302);
        }

        $this->post('/mentee/create', [])->assertStatus(429);
        $this->post('/mentor/create', [])->assertStatus(429);
    }

    public function test_the_email_links_accept_thirty_requests_a_minute() {
        for ($i = 0; $i < 30; $i++) {
            $this->get("/set-status-available/{$i}/nobody@example.test")->assertStatus(200);
        }

        $this->get('/set-status-available/99/nobody@example.test')->assertStatus(429);
        $this->get('/rateMentee/1/1/1')->assertStatus(429);
    }

    public function test_a_second_reset_mail_within_a_minute_is_refused() {
        $user = $this->makeBackofficeUser(1, 'Admin');

        $this->post('/password/email', ['email' => $user->email])->assertSessionHasNoErrors();
        $this->post('/password/email', ['email' => $user->email])->assertSessionHasErrors('email');
    }
}
