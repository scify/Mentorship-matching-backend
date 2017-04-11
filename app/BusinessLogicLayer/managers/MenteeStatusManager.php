<?php

namespace App\BusinessLogicLayer\managers;

use App\StorageLayer\MenteeStatusStorage;

class MenteeStatusManager {

    public $MENTEE_AVAILABLE_ID = 1;
    private $menteeStatusStorage;

    public function __construct() {
        $this->menteeStatusStorage = new MenteeStatusStorage();
    }

    public function getAllMenteeStatuses() {
        return $this->menteeStatusStorage->getMenteeStatuses();
    }
}
