<?php

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\User;
use App\StorageLayer\UserStorage;
use Illuminate\Support\Facades\DB;

/**
 * Class UserManager
 * @package app\BusinessLogicLayer\managers
 * Contains the business logic methods and functionality for the @see User model
 */
class UserManager {

    private $userRoleManager;
    private $userStorage;

    public function __construct() {
        $this->userRoleManager = new UserRoleManager();
        $this->userStorage = new UserStorage();
    }

    public function createUser(array $inputFields) {
        $newUser = new User();
        $newUser->first_name = $inputFields['first_name'];
        $newUser->last_name = $inputFields['last_name'];
        $newUser->email = $inputFields['email'];
        $newUser->password = $inputFields['password'];
        DB::transaction(function() use($newUser, $inputFields) {
            $newUser = $this->userStorage->saveUser($newUser);
            $this->userRoleManager->assignRolesToUser($newUser, $inputFields['user_roles']);
        });
    }

}