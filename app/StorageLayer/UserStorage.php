<?php

namespace App\StorageLayer;

use App\Models\eloquent\AccountManagerCapacity;
use App\Models\eloquent\MentorshipSessionHistory;
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

    public function getCountOfActiveSessionsForAllAccountManagers() {
        $rawQueryStorage = new RawQueryStorage();
        return $rawQueryStorage->performRawQuery("           
            select ms.account_manager_id, count(*) as total_active_sessions  -- count how many active sessions exist per account manager
                from  mentorship_session ms 
                inner join   -- find sessions that have not yet completed
                    (select mentorship_session_id
                     from mentorship_session_history as msh
                        group by mentorship_session_id
                     having
                        max(status_id) <8
                    ) as NonCompletedSessions on ms.id = NonCompletedSessions.mentorship_session_id
                group by ms.account_manager_id
        ");
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

    public function getUsersThatMatchGivenNameOrEmail($searchQuery) {
        return User::where('first_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('email', 'like', '%' . $searchQuery . '%')->get();
    }

    public function getAccountManagersWithAvailableCapacity() {
        $rawQueryStorage = new RawQueryStorage();
        $result = $rawQueryStorage->performRawQuery("
           select u.*, ur.user_id ,capacity ,  (capacity - total_active_sessions) as remainingCapacity from 
                account_manager_capacity amc
                inner join user_role ur on amc.account_manager_id = ur.user_id
                inner join users u on u.id = ur.user_id
                inner join          
                        (select ms.account_manager_id, count(*) as total_active_sessions  -- count how many active sessions exist per account manager
                            from  mentorship_session ms 
                            inner join   -- find sessions that have not yet completed
                                (select mentorship_session_id
                                 from mentorship_session_history as msh
                                    group by mentorship_session_id
                                 having
                                    max(status_id) <8
                                ) as NonCompletedSessions on ms.id = NonCompletedSessions.mentorship_session_id
                            group by ms.account_manager_id
                         ) as activeAccountManagerSessions on activeAccountManagerSessions.account_manager_id = ur.user_id
            
            
            where ur.role_id = 3 
        ");
        return $result;
    }
}
