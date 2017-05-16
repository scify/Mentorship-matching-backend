<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/29/17
 * Time: 6:03 PM
 */

namespace App\StorageLayer;

use App\Models\eloquent\MentorshipSession;
use App\Utils\MentorshipSessionStatuses;

class MentorshipSessionStorage
{
    public function saveMentorshipSession(MentorshipSession $mentorshipSession) {
        $mentorshipSession->save();
        return $mentorshipSession;
    }

    public function deleteMentorshipSession(MentorshipSession $mentorshipSession) {
        $mentorshipSession->delete();
    }

    public function getAllMentorshipSessions() {
        return MentorshipSession::orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionsForMentor($mentorProfileId) {
        return MentorshipSession::where(['mentor_profile_id' => $mentorProfileId])->orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionsForMentee($menteeProfileId) {
        return MentorshipSession::where(['mentee_profile_id' => $menteeProfileId])->orderBy('updated_at', 'desc')->get();
    }

    public function findMentorshipSessionById($id) {
        return MentorshipSession::find($id);
    }

    public function getMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        return MentorshipSession::where(['account_manager_id' => $accountManagerId])->orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionViewModelsForAccountManagerByStatusId($accountManagerId, $mentorshipSessionStatusId) {
        return MentorshipSession::where(['account_manager_id' => $accountManagerId])
            ->whereIn('status_id', $mentorshipSessionStatusId)
            ->orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionViewModelsForMatcher($matcherId) {
        return MentorshipSession::where(['matcher_id' => $matcherId])->orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionsFromIdsArray($filteredMentorshipSessionsIds) {
        return MentorshipSession::whereIn('id', $filteredMentorshipSessionsIds)->get();
    }

    public function getAllActiveMentorshipSessions() {
        $mentorshipSessionStatuses = new MentorshipSessionStatuses();
        return MentorshipSession::whereIn('status_id', $mentorshipSessionStatuses::getActiveSessionStatuses())->get();
    }

    public function getAllCompletedMentorshipSessions() {
        $mentorshipSessionStatuses = new MentorshipSessionStatuses();
        return MentorshipSession::whereIn('status_id', $mentorshipSessionStatuses::getCompletedSessionStatuses())->get();
    }
}
