<?php

namespace App\Models\viewmodels;


use App\BusinessLogicLayer\managers\MentorshipSessionManager;

class MenteeViewModel {

    public $mentee;
    public $avgRating;

    public function __construct($mentee, $withNumberOfTotalSessions) {
        $this->mentee = $mentee;
        $this->mentee->age = intval(date("Y")) - intval($mentee->year_of_birth);

        if($withNumberOfTotalSessions) {
            $mentorshipSessionManager = new MentorshipSessionManager();
            $this->numberOfTotalSessions = $mentorshipSessionManager->getMentorshipSessionsCountForMentee($this->mentee->id);
        }

        $ratings = $mentee->ratings;
        $this->avgRating = !empty($ratings) ? round($ratings->avg('rating')) : null;
    }
}
