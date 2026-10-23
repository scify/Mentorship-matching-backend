<?php

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

/**
 * The reset link in the mail points to APP_URL, never to the Host header of
 * the request that asked for it (Host header poisoning).
 */
class PasswordResetLinkTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_the_reset_link_ignores_a_forged_host() {
        Notification::fake();
        $admin = $this->makeBackofficeUser(1, 'Admin');

        $this->post('http://evil.example/password/email', ['email' => $admin->email])->assertSessionHasNoErrors();

        Notification::assertSentTo($admin, ResetPassword::class, function (ResetPassword $notification) use ($admin) {
            $url = $notification->toMail($admin)->actionUrl;

            return str_starts_with($url, rtrim(config('app.url'), '/') . '/password/reset/') && !str_contains($url, 'evil.example');
        });
    }
}
