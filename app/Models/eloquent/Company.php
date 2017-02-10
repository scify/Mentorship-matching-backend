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
     * @var array
     */
    protected $fillable = ['name', 'description', 'website', 'account_manager_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mentors()
    {
        return $this->hasMany(MentorProfile::class, 'company_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function accountManager()
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
