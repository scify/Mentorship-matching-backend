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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserAccessManager {

    public $ADMINISTRATOR_ROLE_ID = 1;
    public $MATCHER_ROLE_ID = 2;
    public $ACCOUNT_MANAGER_ROLE_ID = 3;

    private $userStorage;

    public function __construct() {
        $this->userStorage = new UserStorage();
    }

    /**
     * Checks if a given @see User has access to create, edit and delete system users
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDSystemUsers(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_admin', $user, $this->ADMINISTRATOR_ROLE_ID);
    }


    /**
     * Checks if a given @see User has access to create, edit and delete Mentors and Mentees
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDMentorsAndMentees(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_admin', $user, $this->ADMINISTRATOR_ROLE_ID);
    }

    /**
     * Checks if a given @see User has access to create, edit and delete @see Company instances
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDCompanies(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_admin', $user, $this->ADMINISTRATOR_ROLE_ID);
    }

    /**
     * Checks if a given @see User has access to change availability status for Mentors and Mentees
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_account_manager', $user, $this->ACCOUNT_MANAGER_ROLE_ID);
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
        return $this->checkCacheOrDBForRoleAndStore('user_is_admin', $user, $this->ADMINISTRATOR_ROLE_ID);
    }

    /**
     * Checks if a given @see User has the "account manager" role
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userIsAccountManager(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_account_manager', $user, $this->ACCOUNT_MANAGER_ROLE_ID);
    }

    /**
     * Checks if a given @see User has the "matcher" role
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userIsMatcher(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_matcher', $user, $this->MATCHER_ROLE_ID);
    }

    /**
     * Checks if a given @see User can edit only the status of a session
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToOnlyEditStatusForMentorshipSessions(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_account_manager', $user, $this->ACCOUNT_MANAGER_ROLE_ID);
    }

    /**
     * Checks if a given @see User can edit whole session
     *
     * @param User $user the @see User instance
     * @return bool
     */
    public function userHasAccessToCRUDMentorshipSessions(User $user) {
        if($user == null)
            return false;
        return $this->checkCacheOrDBForRoleAndStore('user_is_admin', $user, $this->ADMINISTRATOR_ROLE_ID);
    }

    /**
     * Checks if a role (identified by role id) exists in a given collection of @see UserRole
     *
     * @param Collection $userRoles the user roles collection
     * @param int $roleId
     * @return bool
     */
    private function userHasRole(Collection $userRoles, $roleId) {
        return $userRoles->contains($roleId);
    }

    private function checkCacheOrDBForRoleAndStore($roleKey, User $user, $roleId) {
        $result = Cache::get($roleKey . $user->id);
        if($result == null) {
            $userRoles = $user->roles;
            $result = $this->userHasRole($userRoles, $roleId);
            Cache::put($roleKey . $user->id, $result);
        }
        return $result;
    }

}
