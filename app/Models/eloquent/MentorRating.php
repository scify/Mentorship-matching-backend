<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MentorRating extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_rating';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rating', 'rating_description', 'mentor_id', 'session_id', 'rated_by_id', 'created_at', 'updated_at',
    ];

    /**
     * @return HasOne<MentorProfile, $this>
     */
    public function mentor(): HasOne {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_id');
    }

    /**
     * @return HasOne<MenteeProfile, $this>
     */
    public function mentee(): HasOne {
        return $this->hasOne(MenteeProfile::class, 'id', 'rated_by_id');
    }

    /**
     * @return HasOne<MentorshipSession, $this>
     */
    public function session(): HasOne {
        return $this->hasOne(MentorshipSession::class, 'id', 'session_id');
    }
}
