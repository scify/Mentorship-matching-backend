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
        return $this->hasOne('App\Models\UserState', 'id', 'state_id');
    }

    /**
     * Get game versions this user has created
     */
    public function userRoles()
    {
        return $this->hasMany('App\Models\eloquent\UserRole', 'user_id');
    }

    public function userHasAccessToCRUDUser() {
        $userAccessManager = new UserAccessManager();
        return $userAccessManager->userHasAccessToCRUDUser($this);
    }
}
