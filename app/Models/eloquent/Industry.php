<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'industry';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mentors()
    {
        return $this->belongsToMany(MentorProfile::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }
}
