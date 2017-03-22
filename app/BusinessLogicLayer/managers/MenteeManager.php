<?php

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\MenteeProfile;
use App\Models\viewmodels\MenteeViewModel;
use App\StorageLayer\MenteeStorage;
use App\StorageLayer\RawQueryStorage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Gets all the mentees transformed to view model
     *
     * @return Collection MenteeViewModel
     */
    public function getAllMenteeViewModels() {
        $mentees = $this->menteeStorage->getAllMenteeProfiles();
        $menteeViewModels = new Collection();
        foreach ($mentees as $mentee) {
            $menteeViewModels->add($this->getMenteeViewModel($mentee));
        }
        return $menteeViewModels;
    }

    /**
     * Trandform a mentee to the according view model
     *
     * @param MenteeProfile $mentee
     * @return MenteeViewModel
     */
    public function getMenteeViewModel(MenteeProfile $mentee) {
        $menteeViewModel = new MenteeViewModel($mentee);
        return $menteeViewModel;
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

        $loggedInUser = Auth::user();
        if($loggedInUser != null)
            $menteeProfile->creator_user_id = $loggedInUser->id;

        if(isset($inputFields['is_employed']))
            $menteeProfile->is_employed = true;
        else
            $menteeProfile->is_employed = false;


        if(isset($inputFields['job_description']))
            $menteeProfile->job_description = $inputFields['job_description'];
        if(isset($inputFields['university_id']))
            $menteeProfile->university_id = $inputFields['university_id'];
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
            $this->menteeStorage->saveMentee($mentee);
        });
    }

    public function deleteMentee($menteeId) {
        $mentee = $this->getMentee($menteeId);
        $mentee->delete();
    }

    /**
     * Queries the mentees DB table to find string in name or email
     *
     * @param $searchQuery string the name or email that we need to check for
     * @return Collection the mentees that match
     */
    public function filterMenteesByNameAndEmail($searchQuery) {
        return $this->menteeStorage->getMenteesThatMatchGivenNameOrEmail($searchQuery);
    }

    private function transformRawQueryStorageResultsToOneDimensionalArray($results) {
        $temp = array();
        foreach ($results as $result) {
            array_push($temp, $result->id);
        }
        return $temp;
    }

    /**
     * Gets all the filters passed and returns the filtered results
     *
     * @param $filters
     * @return Collection|void|static[]
     * @throws \Exception When filters aren't valid
     */
    private function getMenteesByCriteria($filters) {
        if((!isset($filters['menteeName']) || $filters['menteeName'] === "") &&
            (!isset($filters['universityName']) || $filters['universityName'] === "") &&
            (!isset($filters['completedSessionAgo']) || $filters['completedSessionAgo'] === "") &&
            (!isset($filters['displayOnlyActiveSession']) || $filters['displayOnlyActiveSession'] === 'false') &&
            (!isset($filters['displayOnlyNeverMatched']) || $filters['displayOnlyNeverMatched'] === 'false')) {
            return $this->menteeStorage->getAllMenteeProfiles();
        }
        $whereClauseExists = false;
        $dbQuery = "select distinct mp.id 
            from mentee_profile as mp 
            left outer join mentorship_session as ms on mp.id = ms.mentee_profile_id
            left outer join mentorship_session_history as msh on ms.id = msh.mentorship_session_id ";
        if(isset($filters['displayOnlyActiveSession']) && $filters['displayOnlyActiveSession'] === 'true') {
            $dbQuery .= "left outer join (select ms.id, max(msh.updated_at) as session_date 
	            from mentorship_session_history as msh join mentorship_session as ms on 
	            msh.mentorship_session_id = ms.id join mentee_profile as mp on mp.id = ms.mentee_profile_id 
	            group by mp.id, ms.id)	as last_session on (last_session.id = ms.id and last_session.session_date = msh.updated_at) ";
        }
        $dbQuery .= "where ";
        if(isset($filters['menteeName']) && $filters['menteeName'] != "") {
            $dbQuery .= "(mp.first_name like '%" . $filters['menteeName'] . "%' or mp.last_name like '%" . $filters['menteeName'] . "%') ";
            $whereClauseExists = true;
        }
        if(isset($filters['universityName']) && $filters['universityName'] != "") {
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "mp.university_name like '%" . $filters['universityName'] . "%' ";
        }
        if(isset($filters['displayOnlyActiveSession']) && $filters['displayOnlyActiveSession'] === 'true') {
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "(last_session.session_date is not null and msh.status_id in (1,2,3,4,5,6,7)) ";
        }
        if(isset($filters['completedSessionAgo']) && $filters['completedSessionAgo'] != "") {
            if(intval($filters['completedSessionAgo']) == 0) {
                throw new \Exception("Filter value is not valid.");
            }
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "msh.status_id in (8,9) and msh.updated_at between (now() - interval " . $filters['completedSessionAgo'] . " month) 
                and (now() - interval " . ($filters['completedSessionAgo'] - 1) . " month) ";
            $whereClauseExists = true;
        }
        if(isset($filters['displayOnlyNeverMatched']) && $filters['displayOnlyNeverMatched'] === 'true') {
            if($whereClauseExists) {
                $dbQuery .= "and ";
            }
            $dbQuery .= "ms.id is null ";
        }
        $filteredMenteeIds = $this->transformRawQueryStorageResultsToOneDimensionalArray((new RawQueryStorage())->performRawQuery($dbQuery));
        return $this->menteeStorage->getMenteesFromIdsArray($filteredMenteeIds);
    }

    /**
     * Gets all the filters passed and returns the filtered results as their according view model
     *
     * @param $filters
     * @return Collection MenteeViewModel
     */
    public function getMenteeViewModelsByCriteria($filters) {
        $mentees = $this->getMenteesByCriteria($filters);
        $menteeViewModels = new Collection();
        foreach ($mentees as $mentee) {
            $menteeViewModels->add($this->getMenteeViewModel($mentee));
        }
        return $menteeViewModels;
    }
}
