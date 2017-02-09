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
    protected $fillable = ['name', 'description', 'website'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mentors()
    {
        return $this->hasMany(MentorProfile::class, 'company_id', 'id');
    }
}
