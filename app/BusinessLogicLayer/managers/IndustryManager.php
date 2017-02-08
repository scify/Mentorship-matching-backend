<?php

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorIndustry;
use App\Models\eloquent\MentorProfile;
use App\StorageLayer\IndustryStorage;

class IndustryManager {
    private $industryStorage;

    public function __construct() {
        $this->industryStorage = new IndustryStorage();
    }

    public function getIndustry($id) {
        return $this->industryStorage->getIndustryById($id);
    }

    public function getAllIndustries() {
        return $this->industryStorage->getAllIndustries();
    }

    public function assignIndustriesToMentor(MentorProfile $mentor, $additionalSpecialties) {
        foreach ($additionalSpecialties as $specialty) {
            $this->createNewMentorIndustry($mentor, $specialty['id']);
        }
    }

    private function createNewMentorIndustry(MentorProfile $mentor, $specialtyId) {
        $newMentorIndustry = new MentorIndustry();
        $newMentorIndustry->mentor_profile_id = $mentor->id;
        $newMentorIndustry->industry_id = $specialtyId;
        $this->industryStorage->saveAdditionalSpecialty($newMentorIndustry);
    }

    public function getMentorIndustriesIds(MentorProfile $mentor) {
        $mentorIndustriesIdsArray = array();
        foreach ($mentor->industries as $industry) {
            array_push($mentorIndustriesIdsArray, $industry->id);
        }
        return $mentorIndustriesIdsArray;
    }

    public function editMentorIndustries(MentorProfile $mentor, array $newMentorIndustries) {
        //we get an array of this mentor's industries
        $mentorIndustriesIds = $this->getMentorIndustriesIds($mentor);
        $newMentorIndustriesIds = array();
        //every new industry as an industry id not included
        // in the existing industries of the mentor
        foreach ($newMentorIndustries as $newMentorIndustry) {
            if(!in_array($newMentorIndustry['id'], $mentorIndustriesIds)) {
                //create new role
                $this->createNewMentorIndustry($mentor, $newMentorIndustry['id']);
            }
            array_push($newMentorIndustriesIds, $newMentorIndustry['id']);
        }
        //every industry that was deleted is an industry id in the existing industries
        // not included in the new industries
        foreach ($mentorIndustriesIds as $mentorSpecialtyId) {
            if(!in_array($mentorSpecialtyId, $newMentorIndustriesIds)) {
                //delete industry that was removed
                $this->deleteIndustryFromMentor($mentor, $mentorSpecialtyId);
            }
        }
    }

    public function deleteIndustryFromMentor(MentorProfile $mentor, $industryId) {
        $mentorIndustry = $this->industryStorage->getIndustryForMentor($mentor->id, $industryId);
        $mentorIndustry->delete();
    }
}