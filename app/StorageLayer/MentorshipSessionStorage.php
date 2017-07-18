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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function getDataForExportation() {
        return DB::select(DB::raw('select msession.id, concat(mentor.first_name, " ", mentor.last_name) as mentor_name, 
            concat(mentee.first_name, " ", mentee.last_name) as mentee_name, 
            concat(account_managers.first_name, " ", account_managers.last_name) as account_manager, 
            concat(matchers.first_name, " ", matchers.last_name) as matcher, msession.general_comment, 
            mentorship_session_status_lookup.description as status, msession.created_at as started, 
            msession.updated_at as updated
            from mentorship_session as msession 
            left join mentor_profile as mentor on msession.mentor_profile_id = mentor.id 
            left join mentee_profile as mentee on msession.mentee_profile_id = mentee.id 
            left join users as account_managers on msession.account_manager_id = account_managers.id
            left join users as matchers on msession.matcher_id = matchers.id
            left join mentorship_session_status_lookup on msession.status_id = mentorship_session_status_lookup.id
            where msession.deleted_at is null
            order by msession.id'));
    }

    public function getMentorshipSessionsForFollowUp() {
        $mentorshipSessionStatuses = new MentorshipSessionStatuses();
        $beforeThreeMonthsDate = Carbon::now()->subMonths(3);
        // set time to '00:00:00'
        $beforeThreeMonthsDate->hour(0);
        $beforeThreeMonthsDate->minute(0);
        $beforeThreeMonthsDate->second(0);
        // gets mentorship sessions with matching date and status 'completed - evaluation sent'
        return MentorshipSession::where('updated_at', '>=', date($beforeThreeMonthsDate))
            ->where('updated_at', '<', date($beforeThreeMonthsDate->addDay()))
            ->where('status_id', '=', $mentorshipSessionStatuses::getCompletedSessionStatuses()[0])->get();
    }
}
