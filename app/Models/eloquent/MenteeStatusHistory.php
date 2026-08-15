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
     * @var list<string>
     */
    protected $fillable = ['user_id', 'mentee_profile_id', 'mentee_status_id', 'comment', 'follow_up_date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MenteeProfile, $this>
     */
    public function mentee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MenteeProfile::class, 'id', 'mentee_profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\eloquent\User, $this>
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MenteeStatus, $this>
     */
    public function status(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MenteeStatus::class, 'id', 'mentee_status_id');
    }
}
