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
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Gets all @see MentorProfile instances from the database
     *
     * @return Collection the mentors
     */
    public function getAllMentors() {
        return $this->mentorStorage->getAllMentorProfiles();
    }

    /**
     * Creates a @see MentorProfile resource
     *
     * @param array $inputFields the fields to assign to the mentor
     */
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

    /**
     * Edits (updates) a @see MentorProfile resource, identified by it's id
     *
     * @param array $inputFields the fields to assign to the mentor
     * @param $id int the id of the mentor profile
     */
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

    /**
     * Given a company id and a @see MentorProfile, if the company id is not null
     * then this company gets assigned to the mentor.
     *
     * @param MentorProfile $mentorProfile the mentor profile assign the company to
     * @param $companyId int the id of the company
     */
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

    /**
     * Gets a @see  MentorProfile instance, by id
     *
     * @param $id int the id
     * @return MentorProfile
     */
    public function getMentor($id) {
        return $this->mentorStorage->getMentorProfileById($id);
    }

    /**
     * Soft deletes a given @see MentorProfile, by it's id
     *
     * @param $mentorId int the id
     */
    public function deleteMentor($mentorId) {
        $mentor = $this->getMentor($mentorId);
        $mentor->delete();
    }

    /**
     * Gets all mentors that do not belong to any @see Company
     *
     * @return mixed Collection of mentors
     */
    public function getMentorsWithNoCompanyAssigned() {
        $mentors = $this->mentorStorage->getMentorsByCompanyId(null);
        return $mentors;
    }

    /**
     * Gets all mentors that do not belong to any @see Company, except from those
     * belonging to a given company.
     *
     * @param Company $company the company that will be excluded
     * @return Collection of mentors
     */
    public function getMentorsWithNoCompanyAssignedExceptCompany(Company $company) {
        $mentorsWithNoCompany = $this->mentorStorage->getMentorsByCompanyId(null);
        $mentorsOfThisCompany = $this->mentorStorage->getMentorsByCompanyId($company->id);
        return $mentorsOfThisCompany->merge($mentorsWithNoCompany);
    }

    /**
     * Assigns a given company to a given @see MentorProfile
     *
     * @param Company $company the company that will be assigned to the mentor
     * @param $mentorId int the id of the @see MentorProfile instance
     * @throws \Exception if the given mentor has already a company assigned to them
     */
    public function assignCompanyToMentor(Company $company, $mentorId) {
        $mentor = $this->getMentor($mentorId);
        if($mentor->hasCompany()) {
            throw new \Exception("The mentor " . $mentor->first_name . " " . $mentor->last_name . " has already a company assigned.");
        }
        $mentor->company_id = $company->id;
        $this->mentorStorage->saveMentor($mentor);
    }

    /**
     * Sets the company id of a mentor instance to be null
     *
     * @param $mentorId int the id of the @see MentorProfile instance
     */
    public function unassignCompanyFromMentor($mentorId) {
        $mentor = $this->getMentor($mentorId);
        $mentor->company_id = null;
        $this->mentorStorage->saveMentor($mentor);
    }

    /**
     * Gets mentors satisfying some criteria (for example
     * those who have a specific specialty and name)
     *
     * @param array $input array with criteria values
     * @return Collection|mixed|static[] a collection with mentors satisfying the criteria
     */
    public function getMentorsByCriteria(array $input) {
        $specialtyId = $input['specialty_id'];
        $mentorName = $input['mentor_name'];
        if($specialtyId != null) {
            $mentors = $this->getMentorsWithSpecialty($specialtyId);
        } else {
            $mentors = $this->getAllMentors();
        }
        if($mentorName != null) {
            $mentors = $this->filterMentorsByName($mentors, $mentorName);
        }
        return $mentors;
    }

    /**
     * Gets the mentors having a specified specialty
     *
     * @param $specialtyId int the is of the @see Specialty
     * @return mixed a collection of the mentors with this specialty
     * @throws \Exception if the specialty queried is null
     */
    private function getMentorsWithSpecialty($specialtyId) {
        $specialty = $this->specialtyManager->getSpecialty($specialtyId);
        if($specialty == null)
            throw new \Exception("Specialty not valid");
        return $specialty->mentors;
    }

    /**
     * Queries a mentor collection for a given name
     *
     * @param Collection $mentors a collection of @see MentorProfile instances
     * @param $name string the name to query the collection for
     * @return Collection the subset of the collection, satisfying the query
     */
    private function filterMentorsByName(Collection $mentors, $name) {
        $filteredMentors = $mentors->filter(function ($value, $key) use ($name) {
            $currentMentor = $value;
            $mentorNameSurenameLower = strtolower($currentMentor->first_name . " " . $currentMentor->last_name);
            $queryNameLower = strtolower($name);
            return mb_strpos($mentorNameSurenameLower, $queryNameLower) !== false;
        });
        return $filteredMentors;
    }
}