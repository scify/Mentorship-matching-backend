<?php

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\MenteeStatusHistory;
use App\StorageLayer\MenteeStatusHistoryStorage;

class MenteeStatusHistoryManager {

    private $menteeStatusHistoryStorage;

    public function __construct() {
        $this->menteeStatusHistoryStorage = new MenteeStatusHistoryStorage();
    }

    public function createMenteeStatusHistory($mentee, $statusId, $comment, $followUpDate) {
        $menteeStatusHistory = new MenteeStatusHistory();
        $menteeStatusHistory->user_id = \Auth::id();
        $menteeStatusHistory->mentee_profile_id = $mentee->id;
        $menteeStatusHistory->mentee_status_id = $statusId;
        $menteeStatusHistory->comment = $comment;
        $menteeStatusHistory->follow_up_date = $followUpDate;
        return $this->menteeStatusHistoryStorage->createMenteeStatusHistory($menteeStatusHistory);
    }
}
