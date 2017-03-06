<?php

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\AccountManagerCapacity;
use App\Models\eloquent\Company;
use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\User;
use App\StorageLayer\RoleStorage;
use App\StorageLayer\UserStorage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserManager
 * @package app\BusinessLogicLayer\managers
 * Contains the business logic methods and functionality for the @see User model
 */
class UserManager {

    private $userRoleManager;
    private $userStorage;
    private $roleStorage;

    private $USER_ACTIVATED_STATE_ID = 1;
    private $USER_DEACTIVATED_STATE_ID = 2;

    public function __construct() {
        $this->userRoleManager = new UserRoleManager();
        $this->userStorage = new UserStorage();
        $this->roleStorage = new RoleStorage();
    }

    public function createUser(array $inputFields) {
        $newUser = new User();
        $newUser = $this->assignInputFieldsToUser($newUser, $inputFields);

        DB::transaction(function() use($newUser, $inputFields) {
            $newUser = $this->userStorage->saveUser($newUser);
            $this->userRoleManager->assignRolesToUser($newUser, $inputFields['user_roles']);
        });
        if($newUser->isAccountManager()) {
            $this->accountManagerDetails($newUser, $inputFields);
        }

    }

    private function accountManagerDetails(User $user, array $inputFields) {
        $this->handleCompanyAccountManager($user, $inputFields['company_id']);
        if(isset($inputFields['capacity'])) {
            $this->createOrUpdateAccountManagerCapacity($user->id, $inputFields['capacity']);
        }
    }

    public function getAllUsers() {
        $users = $this->userStorage->getAllUsers();
        return $users;
    }

    private function assignInputFieldsToUser(User $user, array $inputFields) {
        $user->first_name = $inputFields['first_name'];
        $user->last_name = $inputFields['last_name'];
        $user->email = $inputFields['email'];
        //store a has of the password entered
        if($inputFields['password'] != null && $inputFields['password'] != "") {
            $user->password = Hash::make($inputFields['password']);
        }
        return $user;
    }

    public function getUser($id) {
        return $this->userStorage->getUserById($id);
    }

    public function editUser(array $inputFields, $id) {
        $user = $this->getUser($id);
        $user = $this->assignInputFieldsToUser($user, $inputFields);


        DB::transaction(function() use($user, $inputFields) {
            $user = $this->userStorage->saveUser($user);
            $this->handleCompanyAccountManager($user, $inputFields['company_id']);
            $this->userRoleManager->editUserRoles($user, $inputFields['user_roles']);
        });
        //we need to reload the model again, for the isAccountManager
        //to take effect. Because the model we retrieved earlier in this method
        //may have changed it's roles
        $user = $this->getUser($id);
        if($user->isAccountManager()) {
            $this->accountManagerDetails($user, $inputFields);
        }

    }

    private function handleCompanyAccountManager(User $user, $companyId) {
        $companyManager = new CompanyManager();
        if ($companyId == "") {
            $companyManager->removeCompanyFromAccountManager($user);
        } else {
            $companyManager->setAccountManagerToCompany($user, $companyId);
        }
    }

    public function deleteUser($userId) {
        $user = $this->getUser($userId);
        //update user email to include a fixed string so this email
        // can be used again for registering
        $user->email = $user->email . '_deleted';
        $this->userStorage->saveUser($user);
        $user->delete();
    }

    public function activateUser($userId) {
        $user = $this->getUser($userId);
        $user->state_id = $this->USER_ACTIVATED_STATE_ID;
        $this->userStorage->saveUser($user);
    }

    public function deactivateUser($userId) {
        $user = $this->getUser($userId);
        $user->state_id = $this->USER_DEACTIVATED_STATE_ID;
        $this->userStorage->saveUser($user);
    }

    /**
     * Gets all account managers (users with account manager role)
     *
     * @return Collection a collection of @see User
     */
    public function getAllAccountManagers() {
        $userAccessManager = new UserAccessManager();
        $accountManagerRole = $this->userRoleManager->getRoleById($userAccessManager->ACCOUNT_MANAGER_ROLE_ID);
        return $accountManagerRole->users;
    }

    /**
     * Gets all account managers with no company assigned
     *
     * @return Collection a collection of @see User
     */
    public function getAccountManagersWithNoCompanyAssigned() {
        $accountManagersWithNoCompany = new Collection();
        $allAccountManagers = $this->getAllAccountManagers();
        foreach ($allAccountManagers as $accountManager) {
            if(!$accountManager->hasCompany()) {
                $accountManagersWithNoCompany->add($accountManager);
            }
        }
        return $accountManagersWithNoCompany;
    }

    /**
     * Gets all account managers with no company assigned and add the account manager of the current company
     *
     * @param Company $company the company instance
     * @return Collection a collection of @see User
     */
    public function getAccountManagersWithNoCompanyAssignedExceptCurrent(Company $company) {
        //get all account managers with no company assigned and add the account manager of the current company
        $accountManagersWithNoCompany = $this->getAccountManagersWithNoCompanyAssigned();
        $company->accountManager != null ? $accountManagersWithNoCompany->add($company->accountManager) : '';
        return $accountManagersWithNoCompany;
    }

    public function createOrUpdateAccountManagerCapacity($accountManagerId, $capacity) {
        $existingCapacity = $this->userStorage->getAccountManagerCapacityById($accountManagerId);
        // if no record exists
        if($existingCapacity == null) {
            //create new model
            $newAccountManagerCapacity = new AccountManagerCapacity();
            $newAccountManagerCapacity->account_manager_id = $accountManagerId;
            $newAccountManagerCapacity->capacity = $capacity;
            $capacityToBeSaved = $newAccountManagerCapacity;
        } else {
            $existingCapacity->capacity = $capacity;
            $capacityToBeSaved = $existingCapacity;
        }
        $this->userStorage->saveAccountManagerCapacity($capacityToBeSaved);
    }

}