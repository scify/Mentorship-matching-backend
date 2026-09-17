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
     * @var list<string>
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MentorProfile::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }
}
