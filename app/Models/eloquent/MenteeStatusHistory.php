<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MenteeStatusHistory extends Model {
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
     * @return HasOne<MenteeProfile, $this>
     */
    public function mentee(): HasOne {
        return $this->hasOne(MenteeProfile::class, 'id', 'mentee_profile_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function users(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasOne<MenteeStatus, $this>
     */
    public function status(): HasOne {
        return $this->hasOne(MenteeStatus::class, 'id', 'mentee_status_id');
    }
}
