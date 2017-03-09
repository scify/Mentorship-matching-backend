<?php

namespace App\Models\viewmodels;


use App\BusinessLogicLayer\managers\MentorStatusManager;

class CompanyViewModel {

    public $company;
    public $totalMentorsNum;
    public $availableMentorsNum;
    public $matchedMentorsNum;

    public function __construct($company) {
        $this->company = $company;
        $allCompanyMentors = $this->company->mentors;
        $this->totalMentorsNum = $allCompanyMentors->count();

        $mentorStatusManager = new MentorStatusManager();

        $availableMentors = $allCompanyMentors->filter(function ($mentor, $key)  use ($mentorStatusManager) {
            if($mentor->status != null) {
                return $mentor->status->id == $mentorStatusManager->MENTOR_AVAILABLE_ID;
            }
            return false;
        });
        $matchedMentors = $allCompanyMentors->filter(function ($mentor, $key)  use ($mentorStatusManager) {
            if($mentor->status != null) {
                return $mentor->status->id == $mentorStatusManager->MENTOR_MATCHED_ID;
            }
            return false;
        });
        $this->availableMentorsNum = $availableMentors->count();
        $this->matchedMentorsNum = $matchedMentors->count();
    }
}