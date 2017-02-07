<?php

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\MentorSpecialty;
use App\StorageLayer\SpecialtyStorage;

class SpecialtyManager {
    private $specialtyStorage;

    public function __construct() {
        $this->specialtyStorage = new SpecialtyStorage();
    }

    public function getSpecialty($id) {
        return $this->specialtyStorage->getSpecialtyById($id);
    }

    public function getAllSpecialties() {
        return $this->specialtyStorage->getAllSpecialties();
    }

    public function assignSpecialtiesToMentor(MentorProfile $newMentor, $specialties) {
        foreach ($specialties as $specialty) {
            $this->createNewSpecialtyForMentor($newMentor, $specialty['id']);
        }
    }

    private function createNewSpecialtyForMentor(MentorProfile $mentor, $specialtyId) {
        $newMentorSpecialty = new MentorSpecialty();
        $newMentorSpecialty->mentor_profile_id = $mentor->id;
        $newMentorSpecialty->specialty_id = $specialtyId;
        $this->specialtyStorage->saveSpecialty($newMentorSpecialty);
    }
}