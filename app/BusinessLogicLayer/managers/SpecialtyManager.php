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

    public function getPublicSpecialties() {
        return $this->specialtyStorage->getPublicSpecialties();
    }

    public function assignSpecialtiesToMentor(MentorProfile $newMentor, $specialties) {
        foreach ($specialties as $specialty) {
            // if specialty id is a string, that means that we need to store it in the DB before using it
            if (intval($specialty['id']) === 0) {
                $newSpecialtyName = str_replace('new-specialty-', '', $specialty['id']);
                $newSpecialty = $this->specialtyStorage->createSpecialty($newSpecialtyName);
                $specialtyId = $newSpecialty->id;
            } else {
                $specialtyId = $specialty['id'];
            }
            $this->createNewSpecialtyForMentor($newMentor, $specialtyId);
        }
    }

    private function createNewSpecialtyForMentor(MentorProfile $mentor, $specialtyId) {
        $newMentorSpecialty = new MentorSpecialty();
        $newMentorSpecialty->mentor_profile_id = $mentor->id;
        $newMentorSpecialty->specialty_id = $specialtyId;
        $this->specialtyStorage->saveSpecialty($newMentorSpecialty);
    }

    public function getMentorSpecialtiesIds(MentorProfile $mentor) {
        $mentorSpecialtiesIdsArray = array();
        foreach ($mentor->specialties as $specialty) {
            array_push($mentorSpecialtiesIdsArray, $specialty->id);
        }
        return $mentorSpecialtiesIdsArray;
    }

    public function editMentorSpecialties(MentorProfile $mentor, array $newMentorSpecialties) {
        //we get an array of this mentor's specialties
        $mentorSpecialtiesIds = $this->getMentorSpecialtiesIds($mentor);
        $newMentorSpecialtiesIds = array();
        //every new specialty as a specialty id not included
        // in the existing specialties of the mentor
        foreach ($newMentorSpecialties as $newSpecialty) {
            if(!in_array($newSpecialty['id'], $mentorSpecialtiesIds)) {
                //create new role
                $this->createNewSpecialtyForMentor($mentor, $newSpecialty['id']);
            }
            array_push($newMentorSpecialtiesIds, $newSpecialty['id']);
        }
        //every role that was deleted i a role id in the existing roles
        // not included in the new roles
        foreach ($mentorSpecialtiesIds as $mentorSpecialtyId) {
            if(!in_array($mentorSpecialtyId, $newMentorSpecialtiesIds)) {
                //delete role that was removed
                $this->deleteSpecialtyFromMentor($mentor, $mentorSpecialtyId);
            }
        }
    }

    public function deleteSpecialtyFromMentor(MentorProfile $mentor, $specialtyId) {
        $mentorSpecialty = $this->specialtyStorage->getSpecialtyForMentor($mentor->id, $specialtyId);
        $mentorSpecialty->delete();
    }
}
