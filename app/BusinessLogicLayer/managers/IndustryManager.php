<?php

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorIndustry;
use App\Models\eloquent\MentorProfile;
use App\StorageLayer\AdditionalSpecialtyStorage;

class IndustryManager {
    private $additionalSpecialtytorage;

    public function __construct() {
        $this->additionalSpecialtytorage = new AdditionalSpecialtyStorage();
    }

    public function getIndustry($id) {
        return $this->additionalSpecialtytorage->getAdditionalSpecialtyById($id);
    }

    public function getAllIndustries() {
        return $this->additionalSpecialtytorage->getAllAdditionalSpecialties();
    }

    public function assignIndustriesToMentor(MentorProfile $mentor, $additionalSpecialties) {
        foreach ($additionalSpecialties as $specialty) {
            $this->createNewMentorIndustry($mentor, $specialty['id']);
        }
    }

    private function createNewMentorIndustry(MentorProfile $mentor, $specialtyId) {
        $newMentorAdditionalSpecialty = new MentorIndustry();
        $newMentorAdditionalSpecialty->mentor_profile_id = $mentor->id;
        $newMentorAdditionalSpecialty->additional_specialty_id = $specialtyId;
        $this->additionalSpecialtytorage->saveAdditionalSpecialty($newMentorAdditionalSpecialty);
    }
}