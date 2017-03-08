<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MentorStatusComment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_status_comment';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'mentor_profile_id', 'comment', 'follow_up_date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mentor()
    {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
