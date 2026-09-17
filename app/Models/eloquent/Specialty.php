<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'specialty';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MentorProfile::class, 'mentor_specialty')->wherePivot('deleted_at', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\MenteeProfile, $this>
     */
    public function mentees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MenteeProfile::class, 'mentor_specialty', 'id')->wherePivot('deleted_at', null);
    }
}
