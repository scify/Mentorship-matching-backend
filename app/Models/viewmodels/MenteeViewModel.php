<?php

namespace App\Models\viewmodels;


class MenteeViewModel {

    public $mentee;

    public function __construct($mentee) {
        $this->mentee = $mentee;
        $this->mentee->age = intval(date("Y")) - intval($mentee->year_of_birth);
    }
}
