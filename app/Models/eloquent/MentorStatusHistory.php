<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MentorStatusHistory extends Model {
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
     * @return HasOne<MentorProfile, $this>
     */
    public function mentor(): HasOne {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_profile_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function users(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasOne<MentorStatus, $this>
     */
    public function status(): HasOne {
        return $this->hasOne(MentorStatus::class, 'id', 'mentor_status_id');
    }
}
