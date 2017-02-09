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
}