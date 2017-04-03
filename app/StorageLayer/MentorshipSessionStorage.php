<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/29/17
 * Time: 6:03 PM
 */

namespace App\StorageLayer;

use App\Models\eloquent\MentorshipSession;

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

    public function getMentorshipSessionViewModelsForMentor($mentorProfileId) {
        return MentorshipSession::where(['mentor_profile_id' => $mentorProfileId])->orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionViewModelsForMentee($menteeProfileId) {
        return MentorshipSession::where(['mentee_profile_id' => $menteeProfileId])->orderBy('updated_at', 'desc')->get();
    }

    public function findMentorshipSessionById($id) {
        return MentorshipSession::find($id);
    }

    public function getMentorshipSessionViewModelsForAccountManager($accountManagerId) {
        return MentorshipSession::where(['account_manager_id' => $accountManagerId])->orderBy('updated_at', 'desc')->get();
    }

    public function getMentorshipSessionViewModelsForMatcher($matcherId) {
        return MentorshipSession::where(['matcher_id' => $matcherId])->orderBy('updated_at', 'desc')->get();
    }
}
