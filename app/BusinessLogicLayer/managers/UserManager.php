<?php

namespace App\BusinessLogicLayer\managers;

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
        $companyManager = new CompanyManager();

        DB::transaction(function() use($user, $inputFields, $companyManager) {
            $user = $this->userStorage->saveUser($user);
            $this->userRoleManager->editUserRoles($user, $inputFields['user_roles']);
            //TODO: fix
            if($inputFields['company_id'] == "") {
                    $companyManager->removeAccountManagerFromCompany($user);
            } else {
                $companyManager->setAccountManagerToCompany($user, $inputFields['company_id']);
            }
        });
    }

    public function deleteUser($userId) {
        $user = $this->getUser($userId);
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

    public function getAllAccountManagers() {
        $userAccessManager = new UserAccessManager();
        $accountManagerRole = $this->userRoleManager->getRoleById($userAccessManager->ACCOUNT_MANAGER_ROLE_ID);
        return $accountManagerRole->users;
    }

}