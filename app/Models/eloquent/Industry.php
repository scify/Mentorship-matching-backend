<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Industry extends Model {
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
     * @return BelongsToMany<MentorProfile, $this>
     */
    public function mentors(): BelongsToMany {
        return $this->belongsToMany(MentorProfile::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }
}
