<?php

namespace App\Models\eloquent;

use App\BusinessLogicLayer\managers\UserAccessManager;
use app\BusinessLogicLayer\managers\UserManager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'state_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function state() {
        return $this->hasOne(UserState::class, 'id', 'state_id');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'id', 'account_manager_id');
    }

    /**
     * Get @see Role instances this user has
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')->wherePivot('deleted_at', null);
    }

    /**
     * Checks if the user has one of the admin roles.
     *
     * @return bool
     */
    public function isActivated()
    {

        if ( $this->has('state') and ($this->state->id == 1))
        {
            return true;
        }

        return false;
    }

    public function isAdmin() {
        $userAccessManager = new UserAccessManager();
        return $userAccessManager->userIsAdmin($this);
    }

    public function isAccountManager() {
        $userAccessManager = new UserAccessManager();
        return $userAccessManager->userIsAccountManager($this);
    }

    public function userHasAccessToCRUDSystemUser() {
        $userAccessManager = new UserAccessManager();
        return $userAccessManager->userHasAccessToCRUDSystemUser($this);
    }

    public function userHasAccessToCRUDMentorsAndMentees() {
        $userAccessManager = new UserAccessManager();
        return $userAccessManager->userHasAccessToCRUDMentorsAndMentees($this);
    }

    public function userHasAccessToCRUDCompanies() {
        $userAccessManager = new UserAccessManager();
        return $userAccessManager->userHasAccessToCRUDCompanies($this);
    }
}
