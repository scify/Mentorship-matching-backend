<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/31/17
 * Time: 12:22 PM
 */

namespace App\BusinessLogicLayer\managers;

use App\StorageLayer\MentorshipSessionStatusStorage;

class MentorshipSessionStatusManager
{
    private $mentorshipSessionStatusStorage;

    public function __construct() {
        $this->mentorshipSessionStatusStorage = new MentorshipSessionStatusStorage();
    }

    public function getAllMentorshipSessionStatuses() {
        return $this->mentorshipSessionStatusStorage->getAllMentorshipSessionStatuses();
    }
}
