<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenteeStatus extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentee_status_lookup';

    /**
     * @return BelongsTo<MenteeProfile, $this>
     */
    public function mentees(): BelongsTo {
        return $this->belongsTo(MenteeProfile::class, 'status_id', 'id');
    }
}
