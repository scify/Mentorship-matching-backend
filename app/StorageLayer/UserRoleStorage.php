<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 2/2/2017
 * Time: 3:58 μμ
 */

namespace App\StorageLayer;


use App\Models\eloquent\Role;
use App\Models\eloquent\UserRole;

class UserRoleStorage {

    public function getUserRoles() {
        return Role::all();
    }

    public function saveUserRole(UserRole $userRole) {
        $userRole->save();
        return $userRole;
    }

    public function getRoleForUser($userId, $roleId) {
        return UserRole::where(['user_id' => $userId, 'role_id' => $roleId])->firstOrFail();
    }

    public function getUserRolesWithRole($roleId) {
        return UserRole::where(['role_id' => $roleId])->get();
    }
}