<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorIndustry extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_industry';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['mentor_profile_id', 'industry_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MentorProfile::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }
}
