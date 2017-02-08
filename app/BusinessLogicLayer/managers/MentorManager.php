<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 2:03 μμ
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MentorProfile;
use App\StorageLayer\MentorStorage;
use Illuminate\Support\Facades\DB;

class MentorManager {

    private $mentorStorage;
    private $specialtyManager;
    private $industryManager;

    public function __construct() {
        $this->mentorStorage = new MentorStorage();
        $this->specialtyManager = new SpecialtyManager();
        $this->industryManager = new IndustryManager();
    }

    public function getAllMentors() {
        return $this->mentorStorage->getAllMentorProfiles();
    }

    public function createMentor(array $inputFields) {
        $mentorProfile = new MentorProfile();
        $mentorProfile = $this->assignInputFieldsToMentorProfile($mentorProfile, $inputFields);

        DB::transaction(function() use($mentorProfile, $inputFields) {
            $newMentor = $this->mentorStorage->saveMentor($mentorProfile);
            $this->specialtyManager->assignSpecialtiesToMentor($newMentor, $inputFields['specialties']);
            $this->industryManager->assignIndustriesToMentor($newMentor, $inputFields['industries']);
        });
    }

    /**
     * @param MentorProfile $mentorProfile the instance
     * @param array $inputFields the array of input fields
     * @return MentorProfile the instance with the fields assigned
     */
    private function assignInputFieldsToMentorProfile(MentorProfile $mentorProfile, array $inputFields) {
        $mentorProfile->first_name = $inputFields['first_name'];
        $mentorProfile->last_name = $inputFields['last_name'];
        $mentorProfile->age = $inputFields['age'];
        $mentorProfile->address = $inputFields['address'];
        $mentorProfile->email = $inputFields['email'];
        $mentorProfile->skills = $inputFields['skills'];
        $mentorProfile->company = $inputFields['company'];
        $mentorProfile->company_sector = $inputFields['company_sector'];
        $mentorProfile->job_position = $inputFields['job_position'];
        $mentorProfile->job_experience_years = $inputFields['job_experience_years'];
        $mentorProfile->residence_id = $inputFields['residence_id'];

        if(isset($inputFields['university_name']))
            $mentorProfile->university_name = $inputFields['university_name'];
        if(isset($inputFields['university_department_name']))
            $mentorProfile->university_department_name = $inputFields['university_department_name'];
        if(isset($inputFields['linkedin_url']))
            $mentorProfile->linkedin_url = $inputFields['linkedin_url'];
        if(isset($inputFields['phone']))
            $mentorProfile->phone = $inputFields['phone'];
        if(isset($inputFields['cell_phone']))
            $mentorProfile->cell_phone = $inputFields['cell_phone'];
        if(isset($inputFields['reference']))
            $mentorProfile->reference = $inputFields['reference'];

        return $mentorProfile;
    }

    public function getMentor($id) {
        return $this->mentorStorage->getMentorProfileById($id);
    }

    public function editMentor(array $inputFields, $id) {
        $mentor = $this->getMentor($id);
        $mentor = $this->assignInputFieldsToMentorProfile($mentor, $inputFields);

        DB::transaction(function() use($mentor, $inputFields) {
            $mentor = $this->mentorStorage->saveMentor($mentor);
            $this->specialtyManager->editMentorSpecialties($mentor, $inputFields['specialties']);
            $this->industryManager->editMentorIndustries($mentor, $inputFields['industries']);
        });
    }
}