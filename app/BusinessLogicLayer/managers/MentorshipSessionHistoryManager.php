<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/31/17
 * Time: 2:30 PM
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorshipSession;
use App\Models\eloquent\MentorshipSessionHistory;
use App\Models\eloquent\User;
use App\StorageLayer\MentorshipSessionHistoryStorage;

class MentorshipSessionHistoryManager
{
    private $mentorshipSessionHistoryStorage;

    public function __construct() {
        $this->mentorshipSessionHistoryStorage = new MentorshipSessionHistoryStorage();
    }

    public function createMentorshipSessionStatusHistory(MentorshipSession $mentorshipSession, User $loggedInUser, $comment) {
        $mentorshipSessionHistory = new MentorshipSessionHistory();
        $mentorshipSessionHistory->mentorship_session_id = $mentorshipSession->id;
        $mentorshipSessionHistory->user_id = $loggedInUser->id;
        $mentorshipSessionHistory->status_id = $mentorshipSession->status_id;
        $mentorshipSessionHistory->comment = $comment;
        $this->mentorshipSessionHistoryStorage->saveMentorshipSessionHistory($mentorshipSessionHistory);
    }
}
