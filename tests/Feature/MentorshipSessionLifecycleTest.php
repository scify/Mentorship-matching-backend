<?php

use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\User;
use App\Notifications\AccountManagerSessionInvitation;
use App\Notifications\MenteeSendRating;
use App\Notifications\MenteeSessionInvitation;
use App\Notifications\MentorSendRating;
use App\Notifications\MentorSessionInvitation;
use App\Notifications\MentorStatusReactivation;
use App\Utils\MentorshipSessionStatuses;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

/**
 * Characterization tests for the mentorship session state machine.
 *
 * They lock in what the manager does today — status transitions, the audit
 * trail, mentor/mentee availability side effects, and which notification goes
 * to whom — so refactors of MentorshipSessionManager can't silently change
 * the lifecycle. Where behavior looks surprising it is still asserted as-is,
 * with a comment, per Feathers' characterization-test discipline.
 */
class MentorshipSessionLifecycleTest extends TestCase
{
    use CreatesMatchingFixtures;
    use DatabaseTransactions;

    private MentorshipSessionManager $manager;
    private User $matcher;
    private User $accountManager;
    private int $mentorId;
    private int $menteeId;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->manager = new MentorshipSessionManager();
        $this->matcher = $this->makeBackofficeUser(2, 'Matcher');
        $this->accountManager = $this->makeBackofficeUser(3, 'Accman');
        $companyId = $this->makeCompany($this->accountManager, 'Lifecycle Co');
        $this->mentorId = $this->makeMentorProfile($this->matcher, $companyId);
        $this->menteeId = $this->makeMenteeProfile($this->matcher);
    }

    private function createSessionAs(User $creator, User $accountManager): \App\Models\eloquent\MentorshipSession
    {
        $this->be($creator);
        $this->manager->createMentorshipSession([
            'mentor_profile_id' => $this->mentorId,
            'mentee_profile_id' => $this->menteeId,
            'account_manager_id' => $accountManager->id,
            'general_comment' => '', // always present as a form field in the real flow
        ]);
        Auth::logout();
        return \App\Models\eloquent\MentorshipSession::where('mentor_profile_id', $this->mentorId)
            ->orderByDesc('id')->firstOrFail();
    }

    public function testCreatingASessionWithASeparateAccountManagerStartsPendingAndInvitesThem()
    {
        $session = $this->createSessionAs($this->matcher, $this->accountManager);

        $this->assertSame(MentorshipSessionStatuses::$statuses['pending'], (int)$session->status_id);
        $this->assertSame((int)$this->matcher->id, (int)$session->matcher_id);
        $this->assertSame([1], $this->sessionHistoryStatusIds($session->id), 'audit trail: pending only');
        // both sides are taken off the market immediately, before anyone accepts
        $this->assertSame(2, $this->mentorStatusId($this->mentorId), 'mentor -> not available');
        $this->assertSame(2, $this->menteeStatusId($this->menteeId), 'mentee -> matched');
        Notification::assertSentTo($this->accountManager, AccountManagerSessionInvitation::class);
        Notification::assertNotSentTo(MenteeProfile::find($this->menteeId), MenteeSessionInvitation::class);
    }

    public function testCreatingASessionAsOwnAccountManagerSkipsStraightToIntroductionSent()
    {
        $session = $this->createSessionAs($this->matcher, $this->matcher);

        $this->assertSame(MentorshipSessionStatuses::$statuses['introduction_sent'], (int)$session->status_id);
        $this->assertSame([1, 2], $this->sessionHistoryStatusIds($session->id), 'audit trail: pending, then introduction_sent');
        Notification::assertSentTo(MenteeProfile::find($this->menteeId), MenteeSessionInvitation::class);
        Notification::assertNotSentTo($this->matcher, AccountManagerSessionInvitation::class);
    }

    public function testAccountManagerAcceptingAPendingSessionMovesItToIntroductionSent()
    {
        $session = $this->createSessionAs($this->matcher, $this->accountManager);

        $accepted = $this->manager->acceptToManageMentorshipSession(
            $session->id, $this->accountManager->id, $this->accountManager->email);

        $this->assertTrue($accepted);
        $this->assertSame(2, (int)$session->fresh()->status_id);
        Notification::assertSentTo(MenteeProfile::find($this->menteeId), MenteeSessionInvitation::class);
    }

    public function testAccountManagerAcceptLinkWithWrongEmailIsRejected()
    {
        $session = $this->createSessionAs($this->matcher, $this->accountManager);

        $accepted = $this->manager->acceptToManageMentorshipSession(
            $session->id, $this->accountManager->id, 'someone-else@example.test');

        $this->assertFalse($accepted);
        $this->assertSame(1, (int)$session->fresh()->status_id, 'status must not move on a bad link');
    }

    public function testAccountManagerDecliningReleasesMentorAndMentee()
    {
        $session = $this->createSessionAs($this->matcher, $this->accountManager);

        $declined = $this->manager->declineToManageMentorshipSession(
            $session->id, $this->accountManager->id, $this->accountManager->email);

        $this->assertTrue($declined);
        $this->assertSame(MentorshipSessionStatuses::$statuses['cancelled_acc_man'], (int)$session->fresh()->status_id);
        $this->assertSame(1, $this->mentorStatusId($this->mentorId), 'mentor back to available');
        $this->assertSame(1, $this->menteeStatusId($this->menteeId), 'mentee back to available');
    }

    public function testMenteeAcceptingTheIntroductionInvitesTheMentor()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['introduction_sent']);
        $menteeEmail = MenteeProfile::find($this->menteeId)->email;

        $accepted = $this->manager->acceptMentorshipSession($session->id, 'mentee', $this->menteeId, $menteeEmail);

        $this->assertTrue($accepted);
        $this->assertSame(MentorshipSessionStatuses::$statuses['available_mentee'], (int)$session->fresh()->status_id);
        Notification::assertSentTo(MentorProfile::find($this->mentorId), MentorSessionInvitation::class);
    }

    public function testMentorAcceptingAfterTheMenteeCompletesTheHandshake()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['available_mentee']);
        $mentorEmail = MentorProfile::find($this->mentorId)->email;

        $accepted = $this->manager->acceptMentorshipSession($session->id, 'mentor', $this->mentorId, $mentorEmail);

        $this->assertTrue($accepted);
        $this->assertSame(MentorshipSessionStatuses::$statuses['available_mentor'], (int)$session->fresh()->status_id);
    }

    public function testReclickingAStaleAcceptLinkIsIdempotent()
    {
        // Once the session has moved past the status the link was minted for,
        // a re-click reports success and changes nothing.
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['available_mentor']);
        $menteeEmail = MenteeProfile::find($this->menteeId)->email;

        $accepted = $this->manager->acceptMentorshipSession($session->id, 'mentee', $this->menteeId, $menteeEmail);

        $this->assertTrue($accepted, 'stale link re-click is treated as success');
        $this->assertSame(MentorshipSessionStatuses::$statuses['available_mentor'], (int)$session->fresh()->status_id);
        Notification::assertNothingSent();
    }

    public function testAcceptLinkWithWrongEmailIsRejected()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['introduction_sent']);

        $accepted = $this->manager->acceptMentorshipSession($session->id, 'mentee', $this->menteeId, 'wrong@example.test');

        $this->assertFalse($accepted);
        $this->assertSame(MentorshipSessionStatuses::$statuses['introduction_sent'], (int)$session->fresh()->status_id);
    }

    public function testMenteeDecliningCancelsTheSessionAndMarksThemRejected()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['introduction_sent']);
        $menteeEmail = MenteeProfile::find($this->menteeId)->email;

        $declined = $this->manager->declineMentorshipSession($session->id, 'mentee', $this->menteeId, $menteeEmail);

        $this->assertTrue($declined);
        $this->assertSame(MentorshipSessionStatuses::$statuses['cancelled_mentee'], (int)$session->fresh()->status_id);
        $this->assertSame(1, $this->mentorStatusId($this->mentorId), 'mentor is released back to available');
        // This captures current behavior: a mentee who declines is marked
        // "rejected" (4), not "available", and leaves the matching pool.
        $this->assertSame(4, $this->menteeStatusId($this->menteeId));
    }

    public function testMentorDecliningCancelsTheSessionAndReleasesOnlyTheMentee()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['available_mentee']);
        $mentorEmail = MentorProfile::find($this->mentorId)->email;

        $declined = $this->manager->declineMentorshipSession($session->id, 'mentor', $this->mentorId, $mentorEmail);

        $this->assertTrue($declined);
        $this->assertSame(MentorshipSessionStatuses::$statuses['cancelled_mentor'], (int)$session->fresh()->status_id);
        // Current behavior: a declining mentor is parked as "not available".
        $this->assertSame(2, $this->mentorStatusId($this->mentorId));
        $this->assertSame(1, $this->menteeStatusId($this->menteeId), 'mentee back to available');
    }

    public function testReachingTheFourthMeetingAutoAdvancesToEvaluationAndSendsRatings()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['third_meeting']);
        $this->be($this->matcher);

        $message = $this->manager->editMentorshipSession([
            'mentorship_session_id' => $session->id,
            'status_id' => MentorshipSessionStatuses::$statuses['fourth_meeting'],
        ]);

        $this->assertSame(MentorshipSessionStatuses::$statuses['evaluation_sent'], (int)$session->fresh()->status_id,
            'fourth_meeting auto-advances to evaluation_sent in the same edit');
        $this->assertStringContainsString('rate each other', $message);
        $this->assertSame([9, 10], $this->sessionHistoryStatusIds($session->id), 'both steps are audit-trailed');
        $this->assertSame(3, $this->menteeStatusId($this->menteeId), 'mentee -> completed');
        Notification::assertSentTo(MentorProfile::find($this->mentorId), MentorSendRating::class);
        Notification::assertSentTo(MenteeProfile::find($this->menteeId), MenteeSendRating::class);
        Notification::assertSentTo(MentorProfile::find($this->mentorId), MentorStatusReactivation::class);
    }

    public function testDeletingASessionReleasesBothSides()
    {
        $session = $this->makeSessionAtStatus($this->mentorId, $this->menteeId,
            $this->accountManager, $this->matcher, MentorshipSessionStatuses::$statuses['started']);

        $this->manager->deleteMentorshipSession(['mentorship_session_id' => $session->id]);

        $this->assertNull($this->manager->getMentorshipSession($session->id));
        $this->assertSame(1, $this->mentorStatusId($this->mentorId));
        $this->assertSame(1, $this->menteeStatusId($this->menteeId));
    }
}
