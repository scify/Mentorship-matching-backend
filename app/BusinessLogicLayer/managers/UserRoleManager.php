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
}