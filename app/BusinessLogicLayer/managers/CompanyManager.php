<?php

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\Company;
use App\StorageLayer\CompanyStorage;
use Illuminate\Support\Facades\DB;

class CompanyManager {

    private $companyStorage;
    private $specialtyManager;
    private $mentorManager;

    public function __construct() {
        $this->companyStorage = new CompanyStorage();
        $this->specialtyManager = new SpecialtyManager();
        $this->mentorManager = new MentorManager();
    }

    public function getAllCompanies() {
        return $this->companyStorage->getAllCompanies();
    }

    public function createCompany(array $inputFields) {
        $company = new Company();
        $company = $this->assignInputFieldsToCompany($company, $inputFields);

        DB::transaction(function() use($company, $inputFields) {
            $newCompany = $this->companyStorage->saveCompany($company);
            $this->assignCompanyToMentors($newCompany, $inputFields['mentors']);
        });
    }

    /**
     * @param Company $company the instance
     * @param array $inputFields the array of input fields
     * @return Company the instance with the fields assigned
     */
    private function assignInputFieldsToCompany(Company $company, array $inputFields) {
        $company->name = $inputFields['name'];
        if(isset($inputFields['description']))
            $company->description = $inputFields['description'];
        if(isset($inputFields['website']))
            $company->website = $inputFields['website'];
        if(isset($inputFields['account_manager_id']))
            $company->account_manager_id = $inputFields['account_manager_id'];

        return $company;
    }

    public function getCompany($id) {
        return $this->companyStorage->getCompanyById($id);
    }

    public function editCompany(array $inputFields, $id) {
        $company = $this->getCompany($id);
        $company = $this->assignInputFieldsToCompany($company, $inputFields);

        DB::transaction(function() use($company, $inputFields) {
            $company = $this->companyStorage->saveCompany($company);
            $this->editCompanyMentors($company, $inputFields['mentors']);
        });
    }

    public function deleteCompany($companyId) {
        $company = $this->getCompany($companyId);
        $company->delete();
    }

    private function assignCompanyToMentors(Company $company, array $mentors) {
        foreach ($mentors as $mentor) {
            $this->mentorManager->assignCompanyToMentor($company, $mentor['id']);
        }
    }

    public function getCompanyMentorsIds(Company $company) {
        $companyMentorsIds = array();
        foreach ($company->mentors as $mentor) {
            array_push($companyMentorsIds, $mentor->id);
        }
        return $companyMentorsIds;
    }

    private function editCompanyMentors(Company $company, array $newCompanyMentors) {
        //we get an array of this company's mentors
        $companyMentorsIds = $this->getCompanyMentorsIds($company);
        $newCompanyMentorsIds = array();
        //every new mentor as an mentor id not included
        // in the existing mentors of the company
        foreach ($newCompanyMentors as $newCompanyMentor) {
            if(!in_array($newCompanyMentor['id'], $companyMentorsIds)) {
                //assign the company to the new mentor
                $this->mentorManager->assignCompanyToMentor($company, $newCompanyMentor['id']);
            }
            array_push($newCompanyMentorsIds, $newCompanyMentor['id']);
        }
        //every mentor that was removed is a mentor id in the existing mentors
        // not included in the new mentors
        foreach ($companyMentorsIds as $companyMentorId) {
            if(!in_array($companyMentorId, $newCompanyMentorsIds)) {
                //delete company from this mentor
                $this->mentorManager->unassignCompanyFromMentor($companyMentorId);
            }
        }
    }
}