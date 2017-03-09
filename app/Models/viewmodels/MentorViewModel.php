<?php

namespace App\Models\viewmodels;


class MentorViewModel {

    public $mentor;

    public function __construct($mentor) {
        $this->mentor = $mentor;
        $this->mentor->age = intval(date("Y")) - intval($mentor->year_of_birth);
    }
}