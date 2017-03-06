<?php

namespace App\StorageLayer;

use App\Models\eloquent\AccountManagerCapacity;
use App\Models\eloquent\User;

/**
 * Class UserStorage
 * @package app\StorageLayer
 *
 * Contains the eloquent queries methods for the Users.
 */
class UserStorage {

    public function saveUser(User $user) {
        $user->save();
        return $user;
    }

    public function getAllUsers() {
        return User::all();
    }

    public function getUserById($id) {
        return User::find($id);
    }

    public function saveAccountManagerCapacity(AccountManagerCapacity $accountManagerCapacity) {
        $accountManagerCapacity->save();
        return $accountManagerCapacity;
    }

    public function getAccountManagerCapacityById($accountManagerId) {
        return AccountManagerCapacity::where(['account_manager_id' => $accountManagerId])->first();
    }

}