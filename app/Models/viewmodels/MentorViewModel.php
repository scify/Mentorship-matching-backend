<?php

namespace App\Models\viewmodels;


use App\BusinessLogicLayer\managers\MentorshipSessionManager;

class MentorViewModel {

    public $mentor;
    public $avgRating;

    public function __construct($mentor) {
        $this->mentor = $mentor;
        $this->mentor->age = intval(date("Y")) - intval($mentor->year_of_birth);
        $mentorshipSessionManager = new MentorshipSessionManager();

        $this->numberOfTotalSessions = $mentorshipSessionManager->getMentorshipSessionsCountForMentor($this->mentor->id);

        $ratings = $mentor->ratings;
        $this->avgRating = !empty($ratings) ? round($ratings->avg('rating')) : null;
    }
}
