<?php

use App\Models\eloquent\MentorshipSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

/**
 * An admin updates any session. An account manager updates the status and the
 * comments of their own sessions only. Matchers have no edit control.
 */
class SessionUpdateAccessTest extends TestCase {
    use CreatesMatchingFixtures;

    use RefreshDatabase;

    private const STARTED = 5;

    private const FIRST_MEETING = 6;

    protected function setUp(): void {
        parent::setUp();
        Notification::fake();
    }

    public function test_an_account_manager_updates_the_status_of_their_own_session() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $session = $this->makeSession($accountManager);

        $this->actingAs($accountManager)->post('/session/update', $this->update($session));

        $this->assertSame(self::FIRST_MEETING, (int) $session->fresh()->status_id);
    }

    public function test_an_account_manager_cannot_update_another_account_managers_session() {
        $session = $this->makeSession($this->makeBackofficeUser(3, 'Owner'));

        $response = $this->actingAs($this->makeBackofficeUser(3, 'Other'))->post('/session/update', $this->update($session));

        $this->assertSame('error', $response->json('type'));
        $this->assertSame(self::STARTED, (int) $session->fresh()->status_id);
    }

    public function test_a_matcher_cannot_update_a_session() {
        $matcher = $this->makeBackofficeUser(2, 'Matcher');
        $session = $this->makeSession($this->makeBackofficeUser(3, 'Owner'), $matcher);

        $response = $this->actingAs($matcher)->post('/session/update', $this->update($session));

        $this->assertSame('error', $response->json('type'));
        $this->assertSame(self::STARTED, (int) $session->fresh()->status_id);
    }

    public function test_an_account_manager_cannot_reassign_or_delete_their_own_session() {
        $accountManager = $this->makeBackofficeUser(3, 'AccMan');
        $session = $this->makeSession($accountManager);
        $other = $this->makeBackofficeUser(3, 'Other');

        $this->actingAs($accountManager)->post('/session/update', $this->update($session) + [
            'account_manager_id' => $other->id,
            'matcher_id' => $other->id,
            'deleted_at' => now()->toDateTimeString(),
        ]);

        $fresh = MentorshipSession::withTrashed()->find($session->id);
        $this->assertSame($accountManager->id, (int) $fresh->account_manager_id);
        $this->assertNotSame($other->id, (int) $fresh->matcher_id);
        $this->assertNull($fresh->deleted_at);
    }

    public function test_an_admin_updates_any_session_and_its_account_manager() {
        $session = $this->makeSession($this->makeBackofficeUser(3, 'Owner'));
        $newAccountManager = $this->makeBackofficeUser(3, 'New');

        $this->actingAs($this->makeBackofficeUser(1, 'Admin'))->post('/session/update', $this->update($session) + [
            'account_manager_id' => $newAccountManager->id,
            'deleted_at' => now()->toDateTimeString(),
        ]);

        $fresh = MentorshipSession::withTrashed()->find($session->id);
        $this->assertSame(self::FIRST_MEETING, (int) $fresh->status_id);
        $this->assertSame($newAccountManager->id, (int) $fresh->account_manager_id);
        $this->assertNull($fresh->deleted_at);
    }

    private function makeSession($accountManager, $matcher = null): MentorshipSession {
        $admin = $this->makeBackofficeUser(1, 'Creator');

        return $this->makeSessionAtStatus(
            $this->makeMentorProfile($admin, $this->makeCompany($admin, 'Company')),
            $this->makeMenteeProfile($admin),
            $accountManager, $matcher ?? $admin, self::STARTED,
        );
    }

    private function update(MentorshipSession $session): array {
        return ['mentorship_session_id' => $session->id, 'status_id' => self::FIRST_MEETING, 'comment' => 'note', 'general_comment' => 'general'];
    }
}
