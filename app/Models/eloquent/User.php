<?php

namespace App\Models\eloquent;

use App\BusinessLogicLayer\managers\UserAccessManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
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
     * @var list<string>
     */
    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'state_id', 'user_icon_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /** @return HasOne<UserState, $this> */
    public function state(): HasOne {
        return $this->hasOne(UserState::class, 'id', 'state_id');
    }

    /** @return BelongsTo<Company, $this> */
    public function company(): BelongsTo {
        return $this->belongsTo(Company::class, 'id', 'account_manager_id');
    }

    /** @return HasOne<AccountManagerCapacity, $this> */
    public function capacity(): HasOne {
        return $this->hasOne(AccountManagerCapacity::class, 'account_manager_id', 'id');
    }

    /** @return HasOne<UserIcon, $this> */
    public function icon(): HasOne {
        return $this->hasOne(UserIcon::class, 'id', 'user_icon_id');
    }

    /**
     * Get @see Role instances this user has
     *
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'user_role')->wherePivot('deleted_at', null);
    }

    /**
     * Checks if the user has one of the admin roles.
     *
     * @return bool
     */
    public function isActivated() {
        return $this->state !== null && $this->state->id == 1;
    }

    public function isAdmin() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userIsAdmin($this);
    }

    public function hasCompany() {
        return (bool) $this->company()->first();
    }

    public function isCompanyAccountManager() {
        return $this->isAccountManager() && $this->hasCompany();
    }

    public function isAccountManager() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userIsAccountManager($this);
    }

    public function isMatcher() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userIsMatcher($this);
    }

    public function userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessOnlyToChangeAvailabilityStatusForMentorsAndMentees($this);
    }

    public function userHasAccessToEditMentorsAndMentees() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessToEditMentorsAndMentees($this);
    }

    public function userHasAccessToCRUDSystemUsers() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessToCRUDSystemUsers($this);
    }

    public function userHasAccessToCRUDMentorsAndMentees() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessToCRUDMentorsAndMentees($this);
    }

    public function userHasAccessToCRUDCompanies() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessToCRUDCompanies($this);
    }

    public function userHasAccessToOnlyEditStatusForMentorshipSessions() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessToOnlyEditStatusForMentorshipSessions($this);
    }

    public function userHasAccessToCRUDMentorshipSessions() {
        $userAccessManager = new UserAccessManager;

        return $userAccessManager->userHasAccessToCRUDMentorshipSessions($this);
    }
}
