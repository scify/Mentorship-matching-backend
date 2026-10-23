<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'description', 'website', 'hr_contact_details', 'account_manager_id'];

    protected $with = ['mentors'];

    /**
     * @return HasMany<MentorProfile, $this>
     */
    public function mentors(): HasMany {
        return $this->hasMany(MentorProfile::class, 'company_id', 'id');
    }

    /**
     * @return HasOne<User, $this>
     */
    public function accountManager(): HasOne {
        return $this->hasOne(User::class, 'id', 'account_manager_id');
    }

    public function hasAccountManager() {
        return (bool) $this->accountManager()->first();
    }

    public function hasMentors() {
        return (bool) $this->mentors()->first();
    }
}
