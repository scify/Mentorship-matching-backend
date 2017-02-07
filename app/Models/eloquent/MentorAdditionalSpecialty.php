<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MentorAdditionalSpecialty extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_additional_specialty';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mentor_profile_id', 'additional_specialty_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mentors()
    {
        return $this->belongsToMany(MentorProfile::class, 'mentor_additional_specialty')->wherePivot('deleted_at', null);
    }
}
