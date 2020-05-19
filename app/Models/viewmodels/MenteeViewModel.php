<?php

namespace App\Models\viewmodels;


use App\BusinessLogicLayer\managers\MentorshipSessionManager;

class MenteeViewModel {

    public $mentee;
    public $avgRating;

    public function __construct($mentee) {
        $this->mentee = $mentee;
        $this->mentee->age = intval(date("Y")) - intval($mentee->year_of_birth);
        $ratings = $mentee->ratings;
        $this->avgRating = !empty($ratings) ? round($ratings->avg('rating')) : null;
    }
}
