<?php

namespace App\BusinessLogicLayer\managers;


use App\StorageLayer\MentorStatusStorage;

class MentorStatusManager {

    private $mentorStatusStorage;
    public $MENTOR_AVAILABLE_ID = 1;
    public $MENTOR_NOT_AVAILABLE_ID = 2;
    public $MENTOR_MATCHED_ID = 3;
    public $MENTOR_BLACK_LISTED_ID = 4;

    public function __construct() {
        $this->mentorStatusStorage = new MentorStatusStorage();
    }

    public function getMentorStatusesForMentorCreation() {
        return $this->mentorStatusStorage->getMentorStatusesWhereIn([$this->MENTOR_AVAILABLE_ID, $this->MENTOR_NOT_AVAILABLE_ID]);
    }

    public function getAllMentorStatuses() {
        return $this->mentorStatusStorage->getMentorStatuses();
    }
}