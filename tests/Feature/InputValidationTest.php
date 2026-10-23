<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Ids and lookup values from a request must point to existing records of the
 * right kind before the app writes them.
 */
class InputValidationTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        Notification::fake();
    }

    public function test_a_session_needs_an_existing_account_manager() {
        [$matcher, $mentorId, $menteeId] = $this->matchingParties();

        foreach ([999999, $matcher->id] as $accountManagerId) {
            $this->actingAs($matcher)->post('/session/matchMentorWithMentee', [
                'mentor_profile_id' => $mentorId, 'mentee_profile_id' => $menteeId, 'account_manager_id' => $accountManagerId,
            ])->assertSessionHasErrors('account_manager_id');
        }

        $this->assertSame(0, DB::table('mentorship_session')->count());
    }

    public function test_a_session_needs_an_existing_mentor_and_mentee() {
        [$matcher] = $this->matchingParties();
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');

        $this->actingAs($matcher)->post('/session/matchMentorWithMentee', [
            'mentor_profile_id' => 999999, 'mentee_profile_id' => 999998, 'account_manager_id' => $accountManager->id,
        ])->assertSessionHasErrors(['mentor_profile_id', 'mentee_profile_id']);

        $this->assertSame(0, DB::table('mentorship_session')->count());
    }

    public function test_a_status_change_needs_an_existing_status_and_a_valid_date() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $mentorId = $this->makeMentorProfile($accountManager, $this->makeCompany($accountManager, 'Company'));
        $menteeId = $this->makeMenteeProfile($accountManager);
        $this->actingAs($accountManager);

        $this->postJson('/mentor/changeStatus', ['mentor_id' => $mentorId, 'status_id' => 99, 'follow_up_date' => ''])->assertStatus(422);
        $this->postJson('/mentee/changeStatus', ['mentee_id' => $menteeId, 'status_id' => 99, 'follow_up_date' => ''])->assertStatus(422);
        $this->postJson('/mentor/changeStatus', ['mentor_id' => $mentorId, 'status_id' => 2, 'follow_up_date' => 'soon'])->assertStatus(422);

        $this->assertSame(1, (int) DB::table('mentor_profile')->where('id', $mentorId)->value('status_id'));
        $this->assertSame(1, (int) DB::table('mentee_profile')->where('id', $menteeId)->value('status_id'));
    }

    public function test_a_user_needs_an_existing_icon() {
        $admin = $this->makeBackofficeUser(1, 'Admin');

        $this->actingAs($admin)->post('/user/create', [
            'first_name' => 'New', 'last_name' => 'User', 'email' => 'new.user@example.test', 'user_roles' => [['id' => 2]],
            'password' => 'secret123', 'passwordconfirm' => 'secret123', 'usericon' => 'no-such-icon',
        ])->assertSessionHasErrors('usericon');

        $this->actingAs($admin)->post("/user/{$admin->id}/edit", [
            'first_name' => 'Ad', 'last_name' => 'Min', 'email' => $admin->email, 'user_roles' => [['id' => 1]],
            'password' => '', 'passwordconfirm' => '', 'company_id' => '',
        ])->assertSessionHasErrors('usericon');

        $this->assertFalse(DB::table('users')->where('email', 'new.user@example.test')->exists());
    }

    private function matchingParties(): array {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');

        return [$matcher, $this->makeMentorProfile($matcher, $this->makeCompany($matcher, 'Company')), $this->makeMenteeProfile($matcher)];
    }
}
