<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;

/**
 * Login refuses a deactivated user. A password reset logs the user in by
 * itself, so the next request must log a deactivated user out again.
 */
class DeactivatedUserTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    public function test_a_password_reset_does_not_give_a_deactivated_user_access() {
        $user = $this->makeBackofficeUser(1, 'Deactivated');
        $user->state_id = 2;
        $user->save();

        $this->post('/password/reset', [
            'token' => Password::broker()->createToken($user),
            'email' => $user->email,
            'password' => 'new-password-1',
            'password_confirmation' => 'new-password-1',
        ])->assertRedirect();

        $this->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }
}
