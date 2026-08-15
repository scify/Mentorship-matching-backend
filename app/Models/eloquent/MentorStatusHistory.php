<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MentorStatusHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_status_history';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['user_id', 'mentor_profile_id', 'mentor_status_id', 'comment', 'follow_up_date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\eloquent\User, $this>
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorStatus, $this>
     */
    public function status(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MentorStatus::class, 'id', 'mentor_status_id');
    }
}
