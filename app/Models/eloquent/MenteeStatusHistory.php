<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MenteeStatusHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentee_status_history';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'mentee_profile_id', 'mentee_status_id', 'comment', 'follow_up_date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mentee()
    {
        return $this->hasOne(MenteeProfile::class, 'id', 'mentee_profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(MenteeStatus::class, 'id', 'mentee_status_id');
    }
}
