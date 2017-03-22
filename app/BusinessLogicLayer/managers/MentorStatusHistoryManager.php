<?php

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\MentorStatusHistory;
use App\StorageLayer\MentorStatusHistoryStorage;

class MentorStatusHistoryManager {

    private $mentorStatusHistoryStorage;

    public function __construct() {
        $this->mentorStatusHistoryStorage = new MentorStatusHistoryStorage();
    }

    public function createMentorStatusHistory($mentor, $statusId, $comment, $followUpDate) {
        $mentorStatusHistory = new MentorStatusHistory();
        $mentorStatusHistory->user_id = \Auth::id();
        $mentorStatusHistory->mentor_profile_id = $mentor->id;
        $mentorStatusHistory->mentor_status_id = $statusId;
        $mentorStatusHistory->comment = $comment;
        $mentorStatusHistory->follow_up_date = $followUpDate;
        return $this->mentorStatusHistoryStorage->createMentorStatusHistory($mentorStatusHistory);
    }
}
