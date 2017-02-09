<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 2/2/2017
 * Time: 1:57 μμ
 */

namespace App\BusinessLogicLayer\managers;


use App\Models\eloquent\User;
use App\Models\eloquent\UserRole;
use App\StorageLayer\UserStorage;
use Illuminate\Database\Eloquent\Collection;

class UserAccessManager {

    private $ADMINISTRATOR_ROLE_ID = 1;
    private $MATCHER_ROLE_ID = 2;
    public $ACCOUNT_MANAGER_ROLE_ID = 3;

    private $userStorage;

    public function __construct() {
        $this->userStorage = new UserStorage();
    }

    /**
     * Checks if a given @see User has acces to create, edit and delete system users
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDSystemUser(User $user) {
        if($user == null)
            return false;
        $userRoles = $user->roles;
        //only user with admin role
        return $this->userHasRole($userRoles, [$this->ADMINISTRATOR_ROLE_ID]);
    }


    /**
     * Checks if a given @see User has acces to create, edit and delete Mentors and Mentees
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDMentorsAndMentees(User $user) {
        if($user == null)
            return false;
        $userRoles = $user->roles;
        //only user with admin role
        return $this->userHasRole($userRoles, [$this->ADMINISTRATOR_ROLE_ID]);
    }

    /**
     * Checks if a given @see User has acces to create, edit and delete @see Company instances
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDCompanies(User $user) {
        if($user == null)
            return false;
        $userRoles = $user->roles;
        //only user with admin role
        return $this->userHasRole($userRoles, [$this->ADMINISTRATOR_ROLE_ID]);
    }

    /**
     * Checks if a given @see User has the admin role
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userIsAdmin(User $user) {
        if($user == null)
            return false;
        $userRoles = $user->roles;
        //only user with admin role
        return $this->userHasRole($userRoles, [$this->ADMINISTRATOR_ROLE_ID]);
    }

    /**
     * Checks if a role (identified by role id) exists in a given collection of @see UserRole
     *
     * @param Collection $userRoles the user roles collection
     * @param array $roleIds a collection of ints representing roles
     * @return bool
     */
    public function userHasRole(Collection $userRoles, array $roleIds) {
        foreach ($userRoles as $userRole) {
            if(in_array($userRole->id, $roleIds))
                return true;
        }
        return false;
    }
}