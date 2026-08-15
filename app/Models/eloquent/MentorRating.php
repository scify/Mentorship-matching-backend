<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MentorRating extends Model
{
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
        'rating', 'rating_description', 'mentor_id', 'session_id', 'rated_by_id', 'created_at', 'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MenteeProfile, $this>
     */
    public function mentee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MenteeProfile::class, 'id', 'rated_by_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorshipSession, $this>
     */
    public function session(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MentorshipSession::class, 'id', 'session_id');
    }
}
