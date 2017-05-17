<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 2/2/2017
 * Time: 3:57 μμ
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\User;
use App\Models\eloquent\UserRole;
use App\StorageLayer\RoleStorage;
use App\StorageLayer\UserRoleStorage;
use Illuminate\Database\Eloquent\Collection;
use Mockery\Exception;

class UserRoleManager {

    private $userRoleStorage;
    private $roleStorage;

    public function __construct() {
        $this->userRoleStorage = new UserRoleStorage();
        $this->roleStorage = new RoleStorage();
    }

    public function getAllUserRoles() {
        return $this->userRoleStorage->getUserRoles();
    }

    public function assignRolesToUser(User $user, array $userRoles, array $inputFields) {
        $userAccessManager = new UserAccessManager();
        foreach ($userRoles as $userRole) {
            if($userRole['id'] == $userAccessManager->ACCOUNT_MANAGER_ROLE_ID) {
                if(!isset($inputFields['capacity']) || $inputFields['capacity'] == 0 || $inputFields['capacity'] == "") {
                    throw new Exception("Capacity not set for account manager");
                }
            }
            $this->createNewRoleForUser($user, $userRole['id']);
        }
    }

    private function createNewRoleForUser(User $user, $roleId) {
        $newUserRole = new UserRole();
        $newUserRole->user_id = $user->id;
        $newUserRole->role_id = $roleId;
        $this->userRoleStorage->saveUserRole($newUserRole);
    }

    public function getUserRoleIds(User $user) {
        $userRoleIdsArray = array();
        foreach ($user->roles as $role) {
            array_push($userRoleIdsArray, $role->id);
        }
        return $userRoleIdsArray;
    }

    public function deleteRoleFromUser(User $user, $roleId) {
        $userRole = $this->userRoleStorage->getRoleForUser($user->id, $roleId);
        $userRole->delete();
    }

    public function editUserRoles(User $user, array $newUserRoles) {
        //we get an array of this user's roles
        $existingUserRolesIds = $this->getUserRoleIds($user);
        $newRolesIds = array();
        //every new role as a role id not included
        // in the existing roles of the user
        foreach ($newUserRoles as $newRole) {
            if(!in_array($newRole['id'], $existingUserRolesIds)) {
                //create new role
                $this->createNewRoleForUser($user, $newRole['id']);
            }
            array_push($newRolesIds, $newRole['id']);
        }
        //every role that was deleted i a role id in the existing roles
        // not included in the new roles
        foreach ($existingUserRolesIds as $userRoleId) {
            if(!in_array($userRoleId, $newRolesIds)) {
                $userAccessManager = new UserAccessManager();
                //if the role that was removed is the Account manager role
                if($userRoleId == $userAccessManager->ACCOUNT_MANAGER_ROLE_ID) {
                    $companyManager = new CompanyManager();
                    $companyManager->removeCompanyFromAccountManager($user);
                }
                //we should delete the company if the user was Company account manager
                //delete role that was removed
                $this->deleteRoleFromUser($user, $userRoleId);
            }
        }
    }

    public function getUsersByRoleId($roleId) {
        $role = $this->roleStorage->getRoleById($roleId);
        return $role->users;
    }

    public function getRoleById($roleId) {
        return $this->roleStorage->getRoleById($roleId);
    }

}