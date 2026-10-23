<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorStatus extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_status_lookup';

    protected $fillable = ['status', 'description'];

    /**
     * @return BelongsTo<MentorProfile, $this>
     */
    public function mentors(): BelongsTo {
        return $this->belongsTo(MentorProfile::class, 'status_id', 'id');
    }
}
