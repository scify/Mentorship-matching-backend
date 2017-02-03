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
use App\StorageLayer\UserRoleStorage;

class UserRoleManager {

    private $userRoleStorage;

    public function __construct() {
        $this->userRoleStorage = new UserRoleStorage();
    }

    public function getAllUserRoles() {
        return $this->userRoleStorage->getUserRoles();
    }

    public function assignRolesToUser(User $user, array $userRoles) {
        foreach ($userRoles as $userRole) {
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
        foreach ($user->userRoles as $userRole) {
            array_push($userRoleIdsArray, $userRole->role_id);
        }
        return $userRoleIdsArray;
    }

    public function deleteRoleFromUser(User $user, $roleId) {
        $userRole = $this->userRoleStorage->getRoleForUser($user->id, $roleId);
        $userRole->delete();
    }

    public function editUserRoles(User $user, array $newUserRoles) {
        //we get an array of this user's roles
        $userRolesIds = $this->getUserRoleIds($user);
        $newRolesIds = array();
        //every new role as a role id not included
        // in the existing roles of the user
        foreach ($newUserRoles as $newRole) {
            if(!in_array($newRole['id'], $userRolesIds)) {
                //create new role
                $this->createNewRoleForUser($user, $newRole['id']);
            }
            array_push($newRolesIds, $newRole['id']);
        }
        //every role that was deleted i a role id in the existing roles
        // not included in the new roles
        foreach ($userRolesIds as $userRoleId) {
            if(!in_array($userRoleId, $newRolesIds)) {
                //delete role that was removed
                $this->deleteRoleFromUser($user, $userRoleId);
            }
        }
    }
}