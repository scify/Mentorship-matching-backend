<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'education_level';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
//     */
//    public function mentors()
//    {
//        return $this->belongsToMany(MentorProfile::class, 'mentor_specialty')->wherePivot('deleted_at', null);
//    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentees()
    {
        return $this->belongsTo(MenteeProfile::class, 'education_level_id', 'id');
    }
}
