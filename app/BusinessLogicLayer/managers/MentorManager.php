<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 2:03 μμ
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\Company;
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
            $this->handleMentorCompany($newMentor, $inputFields['company_id']);
        });
    }

    public function editMentor(array $inputFields, $id) {
        $mentor = $this->getMentor($id);
        $mentor = $this->assignInputFieldsToMentorProfile($mentor, $inputFields);

        DB::transaction(function() use($mentor, $inputFields) {
            $mentor = $this->mentorStorage->saveMentor($mentor);
            $this->specialtyManager->editMentorSpecialties($mentor, $inputFields['specialties']);
            $this->industryManager->editMentorIndustries($mentor, $inputFields['industries']);
            $this->handleMentorCompany($mentor, $inputFields['company_id']);
        });
    }

    private function handleMentorCompany(MentorProfile $mentorProfile, $companyId) {
        if(isset($companyId)) {
            if ($companyId == "") {
                $mentorProfile->company_id = null;
            } else {
                $mentorProfile->company_id = $companyId;
            }
        }
        $this->mentorStorage->saveMentor($mentorProfile);
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
        $mentorProfile->company_name = $inputFields['company_name'];
        $mentorProfile->company_sector = $inputFields['company_sector'];
        $mentorProfile->job_position = $inputFields['job_position'];
        $mentorProfile->job_experience_years = $inputFields['job_experience_years'];
        $mentorProfile->residence_id = $inputFields['residence_id'];

        if(isset($inputFields['is_available']))
            $mentorProfile->is_available = true;
        else
            $mentorProfile->is_available = false;

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

    public function deleteMentor($mentorId) {
        $mentor = $this->getMentor($mentorId);
        $mentor->delete();
    }

    public function getMentorsWithNoCompanyAssigned() {
        $mentors = $this->mentorStorage->getMentorsByCompanyId(null);
        return $mentors;
    }

    public function getMentorsWithNoCompanyAssignedExceptCompany(Company $company) {
        $mentorsWithNoCompany = $this->mentorStorage->getMentorsByCompanyId(null);
        $mentorsOfThisCompany = $this->mentorStorage->getMentorsByCompanyId($company->id);
        return $mentorsOfThisCompany->merge($mentorsWithNoCompany);
    }

    public function assignCompanyToMentor(Company $company, $mentorId) {
        $mentor = $this->getMentor($mentorId);
        if($mentor->hasCompany()) {
            throw new \Exception("The mentor " . $mentor->first_name . " " . $mentor->last_name . " has already a company assigned.");
        }
        $mentor->company_id = $company->id;
        $this->mentorStorage->saveMentor($mentor);
    }

    public function unassignCompanyFromMentor($mentorId) {
        $mentor = $this->getMentor($mentorId);
        $mentor->company_id = null;
        $this->mentorStorage->saveMentor($mentor);
    }
}