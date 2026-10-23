<?php

use App\Models\eloquent\MentorshipSession;
use App\Notifications\MenteeSessionInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

/**
 * "Confirm availability" in the session modal invites the mentee of a pending
 * session. It is a POST, for an admin or the session's account manager.
 */
class InviteMenteeTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const PENDING = 1;

    private const INTRODUCTION_SENT = 2;

    protected function setUp(): void {
        parent::setUp();
        Notification::fake();
    }

    public function test_the_account_manager_invites_the_mentee_of_their_pending_session() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $session = $this->makeSession($accountManager, self::PENDING);

        $this->actingAs($accountManager)->post('/session/sendInviteToMentee', ['mentorship_session_id' => $session->id]);

        $this->assertSame(self::INTRODUCTION_SENT, (int) $session->fresh()->status_id);
        Notification::assertSentTo($session->mentee, MenteeSessionInvitation::class);
    }

    public function test_an_admin_invites_the_mentee_of_any_pending_session() {
        $session = $this->makeSession($this->makeBackofficeUser(3, 'AccMan'), self::PENDING);

        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))->post('/session/sendInviteToMentee', ['mentorship_session_id' => $session->id]);

        $this->assertSame(self::INTRODUCTION_SENT, (int) $session->fresh()->status_id);
    }

    public function test_another_account_manager_cannot_invite() {
        $session = $this->makeSession($this->makeBackofficeUser(3, 'Owner'), self::PENDING);

        $this->actingAs($this->makeBackofficeUser(3, 'Other'))->post('/session/sendInviteToMentee', ['mentorship_session_id' => $session->id])
            ->assertSessionHas('flash_message_failure');

        $this->assertSame(self::PENDING, (int) $session->fresh()->status_id);
        Notification::assertNothingSent();
    }

    public function test_a_session_that_is_not_pending_is_not_reset() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $session = $this->makeSession($accountManager, 5);

        $this->actingAs($accountManager)->post('/session/sendInviteToMentee', ['mentorship_session_id' => $session->id]);

        $this->assertSame(5, (int) $session->fresh()->status_id);
        Notification::assertNothingSent();
    }

    public function test_a_get_request_changes_nothing() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $session = $this->makeSession($accountManager, self::PENDING);

        $this->actingAs($accountManager)->get('/session/sendInviteToMentee?session_id=' . $session->id)->assertStatus(405);

        $this->assertSame(self::PENDING, (int) $session->fresh()->status_id);
    }

    private function makeSession($accountManager, int $statusId): MentorshipSession {
        $admin = $this->makeBackofficeUser(1, 'Creator');

        return $this->makeSessionAtStatus(
            $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company')),
            $this->makeMenteeProfile($admin),
            $accountManager, $admin, $statusId,
        );
    }
}
