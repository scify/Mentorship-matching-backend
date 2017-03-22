<?php

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\Company;
use App\Models\eloquent\User;
use App\Models\viewmodels\CompanyViewModel;
use App\StorageLayer\CompanyStorage;
use Illuminate\Database\Eloquent\Collection;
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

        // return the newly created company
        return (DB::transaction(function() use($company, $inputFields) {
            $newCompany = $this->companyStorage->saveCompany($company);
            if(isset($inputFields['mentors']))
                $this->assignCompanyToMentors($newCompany, $inputFields['mentors']);
            if(isset($inputFields['account_manager_id']))
                $this->handleCompanyAccountManager($company, $inputFields['account_manager_id']);
            return $newCompany;
        }));
    }

    public function editCompany(array $inputFields, $id) {
        $company = $this->getCompany($id);
        $company = $this->assignInputFieldsToCompany($company, $inputFields);

        DB::transaction(function() use($company, $inputFields) {
            $company = $this->companyStorage->saveCompany($company);
            //if the mentors of input fields does not exist
            //it means that either the user dod not choose any mentors for this company
            //or that the user removed all mentors from the company.
            //so we need to pass an empty array as the selected company mentors.
            if(!isset($inputFields['mentors'])) {
                $inputFields['mentors'] = array();
            }
            $this->editCompanyMentors($company, $inputFields['mentors']);
            if(isset($inputFields['account_manager_id']))
                $this->handleCompanyAccountManager($company, $inputFields['account_manager_id']);
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
        if(isset($inputFields['hr_contact_details']))
            $company->hr_contact_details = $inputFields['hr_contact_details'];

        return $company;
    }

    public function getCompany($id) {
        return $this->companyStorage->getCompanyById($id);
    }

    /**
     * Given an account manager id, decides if the company account manager should be updated, deleted, or created
     *
     * @param Company $company
     * @param $accountManagerId
     */
    private function handleCompanyAccountManager(Company $company, $accountManagerId) {
        if(isset($accountManagerId)) {
            //null account manager id, remove account manager from company if exists
            if ($accountManagerId == "") {
                $this->removeAccountManagerFromCompany($company);
            } else {
                //if given account manager id is different from the existing account manager id, update
                //or create, if the existing account manager id was null.
                if($accountManagerId != $company->account_manager_id) {
                    $userManager = new UserManager();
                    $user = $userManager->getUser($accountManagerId);
                    $this->setAccountManagerToCompany($user, $accountManagerId);
                }
            }
        }
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


    public function getAllUnassignedCompanies() {
        $mentors = $this->companyStorage->getCompaniesByAccountManagerId(null);
        return $mentors;
    }

    public function getCompaniesWithNoAccountManagerAssignedExceptAccountManager(User $accountManager) {
        $companiesWithNoAccountManager = $this->companyStorage->getCompaniesByAccountManagerId(null);
        if($accountManager->company != null)
            $companiesWithNoAccountManager->add($accountManager->company);
        return $companiesWithNoAccountManager;
    }

    public function setAccountManagerToCompany(User $accountManager, $companyId) {

        $company = $this->getCompany($companyId);
        if($accountManager->id != $company->account_manager_id) {
            //check if the user has already a company assigned
            if ($accountManager->hasCompany()) {
                throw new \Exception("The user " . $accountManager->first_name . " " . $accountManager->last_name . " has already a company assigned.");
            }

            // remove old account manager from company
            $this->removeAccountManagerFromCompany($company);
            $company->account_manager_id = $accountManager->id;
            $this->companyStorage->saveCompany($company);
        }
    }

    public function removeAccountManagerFromCompany (Company $company) {
        $company->account_manager_id = null;
        $this->companyStorage->saveCompany($company);
    }

    public function removeCompanyFromAccountManager(User $user) {
        $company = $this->companyStorage->getCompaniesByAccountManagerId($user->id)->first();
        if($company != null) {
            $this->removeAccountManagerFromCompany($company);
        }
    }

    public function getAllCompanyViewModels() {
        $companies = $this->getAllCompanies();
        $companyViewModels = new Collection();
        foreach ($companies as $company) {
            $companyViewModels->add($this->getCompanyViewModel($company));
        }
        return $companyViewModels;
    }

    public function getCompanyViewModel(Company $company) {
        $companyViewModel = new CompanyViewModel($company);
        return $companyViewModel;
    }
}
