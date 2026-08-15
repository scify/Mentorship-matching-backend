<?php

use App\Utils\MentorshipSessionStatuses;

/**
 * Locks in the session status id contract. These ids are hardcoded across the
 * managers, the seeders and several raw queries — renumbering any of them is a
 * data migration, not a refactor, and this test is the tripwire for that.
 */
class MentorshipSessionStatusesTest extends TestCase
{
    public function testStatusIdsMatchTheSeededLookupTable()
    {
        $this->assertSame([
            'pending' => 1,
            'introduction_sent' => 2,
            'available_mentee' => 3,
            'available_mentor' => 4,
            'started' => 5,
            'first_meeting' => 6,
            'second_meeting' => 7,
            'third_meeting' => 8,
            'fourth_meeting' => 9,
            'evaluation_sent' => 10,
            'follow_up_sent' => 11,
            'cancelled_mentee' => 12,
            'cancelled_mentor' => 13,
            'cancelled_acc_man' => 14,
        ], MentorshipSessionStatuses::$statuses);
    }

    public function testActiveBucketCoversPendingThroughFourthMeeting()
    {
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9], MentorshipSessionStatuses::getActiveSessionStatuses());
    }

    public function testCompletedBucketIsEvaluationAndFollowUp()
    {
        $this->assertSame([10, 11], MentorshipSessionStatuses::getCompletedSessionStatuses());
    }

    public function testCancelledBucketCoversAllThreeCancellationRoles()
    {
        $this->assertSame([12, 13, 14], MentorshipSessionStatuses::getCancelledSessionStatuses());
    }

    public function testPendingBucketIsASubsetOfActive()
    {
        foreach (MentorshipSessionStatuses::getPendingSessionStatuses() as $statusId) {
            $this->assertContains($statusId, MentorshipSessionStatuses::getActiveSessionStatuses());
        }
    }

    public function testEveryStatusBelongsToExactlyOneOfActiveCompletedCancelled()
    {
        $active = MentorshipSessionStatuses::getActiveSessionStatuses();
        $completed = MentorshipSessionStatuses::getCompletedSessionStatuses();
        $cancelled = MentorshipSessionStatuses::getCancelledSessionStatuses();

        $union = array_merge($active, $completed, $cancelled);
        sort($union);
        $this->assertSame(array_values(MentorshipSessionStatuses::$statuses), $union,
            'active/completed/cancelled must partition the full status set');
        $this->assertSame([], array_intersect($active, $completed));
        $this->assertSame([], array_intersect($active, $cancelled));
        $this->assertSame([], array_intersect($completed, $cancelled));
    }
}
