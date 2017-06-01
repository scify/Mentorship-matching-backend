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
     * @var array
     */
    protected $fillable = [
        'rating', 'rating_description', 'mentor_id', 'session_id', 'rated_by_id', 'created_at', 'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mentor()
    {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mentee()
    {
        return $this->hasOne(MenteeProfile::class, 'id', 'rated_by_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function session()
    {
        return $this->hasOne(MentorshipSession::class, 'id', 'session_id');
    }
}
