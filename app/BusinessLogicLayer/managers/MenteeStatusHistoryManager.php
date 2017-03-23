<?php

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\MenteeStatusHistory;
use App\Models\eloquent\User;
use App\StorageLayer\MenteeStatusHistoryStorage;

class MenteeStatusHistoryManager {

    private $menteeStatusHistoryStorage;

    public function __construct() {
        $this->menteeStatusHistoryStorage = new MenteeStatusHistoryStorage();
    }

    public function createMenteeStatusHistory(MenteeProfile $mentee, $statusId, $comment, $followUpDate, User $loggedInUser) {
        $menteeStatusHistory = new MenteeStatusHistory();
        $menteeStatusHistory->user_id = ($loggedInUser != null) ? $loggedInUser->id : null;
        $menteeStatusHistory->mentee_profile_id = $mentee->id;
        $menteeStatusHistory->mentee_status_id = $statusId;
        $menteeStatusHistory->comment = $comment;
        $menteeStatusHistory->follow_up_date = $followUpDate;
        return $this->menteeStatusHistoryStorage->createMenteeStatusHistory($menteeStatusHistory);
    }
}
