<?php

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\MentorSpecialty;
use App\Models\eloquent\MenteeSpecialty;
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

    public function assignSpecialtiesToMentee(MenteeProfile $newMentee, $specialties) {
        foreach ($specialties as $specialty) {
            // if specialty id is a string, that means that we need to store it in the DB before using it
            if (intval($specialty['id']) === 0) {
                $newSpecialtyName = str_replace('new-specialty-', '', $specialty['id']);
                $newSpecialty = $this->specialtyStorage->createSpecialty($newSpecialtyName);
                $specialtyId = $newSpecialty->id;
            } else {
                $specialtyId = $specialty['id'];
            }
            $this->createNewSpecialtyForMentee($newMentee, $specialtyId);
        }
    }

    private function createNewSpecialtyForMentor(MentorProfile $mentor, $specialtyId) {
        $newMentorSpecialty = new MentorSpecialty();
        $newMentorSpecialty->mentor_profile_id = $mentor->id;
        $newMentorSpecialty->specialty_id = $specialtyId;
        $this->specialtyStorage->saveMentorSpecialty($newMentorSpecialty);
    }

    private function createNewSpecialtyForMentee(MenteeProfile $mentee, $specialtyId) {
        $newMenteeSpecialty = new MenteeSpecialty();
        $newMenteeSpecialty->mentee_profile_id = $mentee->id;
        $newMenteeSpecialty->specialty_id = $specialtyId;
        $this->specialtyStorage->saveMenteeSpecialty($newMenteeSpecialty);
    }

    public function getMentorSpecialtiesIds(MentorProfile $mentor) {
        $mentorSpecialtiesIdsArray = array();
        foreach ($mentor->specialties as $specialty) {
            array_push($mentorSpecialtiesIdsArray, $specialty->id);
        }
        return $mentorSpecialtiesIdsArray;
    }

    public function getMenteeSpecialtiesIds(MenteeProfile $mentee) {
        $menteeSpecialtiesIdsArray = array();
        foreach ($mentee->specialties as $specialty) {
            array_push($menteeSpecialtiesIdsArray, $specialty->id);
        }
        return $menteeSpecialtiesIdsArray;
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

    public function editMenteeSpecialties(MenteeProfile $mentee, array $newMenteeSpecialties) {
        //we get an array of this mentor's specialties
        $menteeSpecialtiesIds = $this->getMenteeSpecialtiesIds($mentee);
        $newMenteeSpecialtiesIds = array();
        //every new specialty as a specialty id not included
        // in the existing specialties of the mentor
        foreach ($newMenteeSpecialties as $newSpecialty) {
            if(!in_array($newSpecialty['id'], $menteeSpecialtiesIds)) {
                //create new role
                $this->createNewSpecialtyForMentee($mentee, $newSpecialty['id']);
            }
            array_push($newMenteeSpecialtiesIds, $newSpecialty['id']);
        }
        //every role that was deleted i a role id in the existing roles
        // not included in the new roles
        foreach ($menteeSpecialtiesIds as $mentorSpecialtyId) {
            if(!in_array($mentorSpecialtyId, $newMenteeSpecialtiesIds)) {
                //delete role that was removed
                $this->deleteSpecialtyFromMentee($mentee, $mentorSpecialtyId);
            }
        }
    }

    public function deleteSpecialtyFromMentor(MentorProfile $mentor, $specialtyId) {
        $mentorSpecialty = $this->specialtyStorage->getSpecialtyForMentor($mentor->id, $specialtyId);
        $mentorSpecialty->delete();
    }

    public function deleteSpecialtyFromMentee(MenteeProfile $mentee, $specialtyId) {
        $menteeSpecialty = $this->specialtyStorage->getSpecialtyForMentee($mentee->id, $specialtyId);
        $menteeSpecialty->delete();
    }


}
