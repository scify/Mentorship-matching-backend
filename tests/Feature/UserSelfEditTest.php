<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * A non-admin who changes their own email or password must enter the current
 * password, so a stolen session cannot take over the account.
 */
class UserSelfEditTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const NEW_PASSWORD = 'a-much-longer-passphrase-than-twelve';

    public function test_a_password_change_needs_the_current_password() {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');
        $this->actingAs($matcher);
        $change = ['password' => self::NEW_PASSWORD, 'passwordconfirm' => self::NEW_PASSWORD] + $this->profile($matcher);

        $this->post("/user/{$matcher->id}/edit", $change)->assertSessionHasErrors('current_password');
        $this->post("/user/{$matcher->id}/edit", $change + ['current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('phpunit-password', $matcher->fresh()->password));

        $this->post("/user/{$matcher->id}/edit", $change + ['current_password' => 'phpunit-password'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $matcher->fresh()->password));
    }

    public function test_an_email_change_needs_the_current_password() {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');

        $this->actingAs($matcher)->post("/user/{$matcher->id}/edit", ['email' => 'taken.over@example.test'] + $this->profile($matcher))
            ->assertSessionHasErrors('current_password');

        $this->assertSame($matcher->email, DB::table('users')->where('id', $matcher->id)->value('email'));
    }

    public function test_other_changes_need_no_password() {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');

        $this->actingAs($matcher)->post("/user/{$matcher->id}/edit", ['first_name' => 'Renamed'] + $this->profile($matcher))
            ->assertSessionHasNoErrors();

        $this->assertSame('Renamed', $matcher->fresh()->first_name);
    }

    public function test_an_admin_sets_another_users_password_without_theirs() {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');

        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))
            ->post("/user/{$matcher->id}/edit", ['user_roles' => [['id' => 2]], 'password' => self::NEW_PASSWORD, 'passwordconfirm' => self::NEW_PASSWORD] + $this->profile($matcher))
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $matcher->fresh()->password));
    }

    private function profile($user): array {
        return [
            'first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->email,
            'usericon' => DB::table('user_icon')->value('title'), 'company_id' => '', 'password' => '', 'passwordconfirm' => '',
        ];
    }
}
