<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MentorProfile::class, 'company_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\User, $this>
     */
    public function accountManager(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'account_manager_id');
    }

    public function hasAccountManager(){

        return (bool) $this->accountManager()->first();
    }

    public function hasMentors(){

        return (bool) $this->mentors()->first();
    }
}
