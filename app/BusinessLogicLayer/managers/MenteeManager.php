<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 2:03 μμ
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\MenteeProfile;
use App\StorageLayer\MenteeStorage;
use Illuminate\Support\Facades\DB;

class MenteeManager {

    private $menteeStorage;
    private $specialtyManager;
    private $industryManager;

    public function __construct() {
        $this->menteeStorage = new MenteeStorage();
        $this->specialtyManager = new SpecialtyManager();
    }

    public function getAllMentees() {
        return $this->menteeStorage->getAllMenteeProfiles();
    }

    public function createMentee(array $inputFields) {
        $menteeProfile = new MenteeProfile();
        $menteeProfile = $this->assignInputFieldsToMenteeProfile($menteeProfile, $inputFields);

        DB::transaction(function() use($menteeProfile, $inputFields) {
            $newMentee = $this->menteeStorage->saveMentee($menteeProfile);
        });
    }

    /**
     * @param MenteeProfile $menteeProfile the instance
     * @param array $inputFields the array of input fields
     * @return MenteeProfile the instance with the fields assigned
     */
    private function assignInputFieldsToMenteeProfile(MenteeProfile $menteeProfile, array $inputFields) {
        $menteeProfile->first_name = $inputFields['first_name'];
        $menteeProfile->last_name = $inputFields['last_name'];
        $menteeProfile->year_of_birth = $inputFields['year_of_birth'];
        $menteeProfile->address = $inputFields['address'];
        $menteeProfile->email = $inputFields['email'];
        $menteeProfile->specialty_id = $inputFields['specialty_id'];
        $menteeProfile->specialty_experience = $inputFields['specialty_experience'];
        $menteeProfile->expectations = $inputFields['expectations'];
        $menteeProfile->career_goals = $inputFields['career_goals'];
        $menteeProfile->residence_id = $inputFields['residence_id'];


        if(isset($inputFields['is_employed']))
            $menteeProfile->is_employed = true;
        else
            $menteeProfile->is_employed = false;


        if(isset($inputFields['job_description']))
            $menteeProfile->job_description = $inputFields['job_description'];
        if(isset($inputFields['university_name']))
            $menteeProfile->university_name = $inputFields['university_name'];
        if(isset($inputFields['university_graduation_year']))
            $menteeProfile->university_graduation_year = $inputFields['university_graduation_year'];
        if(isset($inputFields['university_department_name']))
            $menteeProfile->university_department_name = $inputFields['university_department_name'];
        if(isset($inputFields['linkedin_url']))
            $menteeProfile->linkedin_url = $inputFields['linkedin_url'];
        if(isset($inputFields['phone']))
            $menteeProfile->phone = $inputFields['phone'];
        if(isset($inputFields['cell_phone']))
            $menteeProfile->cell_phone = $inputFields['cell_phone'];
        if(isset($inputFields['reference']))
            $menteeProfile->reference = $inputFields['reference'];

        return $menteeProfile;
    }

    public function getMentee($id) {
        $mentee = $this->menteeStorage->getMenteeProfileById($id);
        $mentee->age = intval(date("Y")) - intval($mentee->year_of_birth);
        return $mentee;
    }

    public function editMentee(array $inputFields, $id) {
        $mentee = $this->getMentee($id);
        $mentee = $this->assignInputFieldsToMenteeProfile($mentee, $inputFields);

        DB::transaction(function() use($mentee, $inputFields) {
            $mentee = $this->menteeStorage->saveMentee($mentee);
        });
    }

    public function deleteMentee($menteeId) {
        $mentee = $this->getMentee($menteeId);
        $mentee->delete();
    }
}