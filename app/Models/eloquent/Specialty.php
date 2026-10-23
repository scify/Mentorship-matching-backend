<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialty extends Model {
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
     * @return BelongsToMany<MentorProfile, $this>
     */
    public function mentors(): BelongsToMany {
        return $this->belongsToMany(MentorProfile::class, 'mentor_specialty')->wherePivot('deleted_at', null);
    }

    /**
     * @return BelongsToMany<MenteeProfile, $this>
     */
    public function mentees(): BelongsToMany {
        return $this->belongsToMany(MenteeProfile::class, 'mentor_specialty', 'id')->wherePivot('deleted_at', null);
    }
}
