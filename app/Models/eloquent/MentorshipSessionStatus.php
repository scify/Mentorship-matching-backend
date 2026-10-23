<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MentorshipSessionStatus extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentorship_session_status_lookup';

    protected $guarded = ['id'];

    /**
     * @return BelongsToMany<MentorshipSessionHistory, $this>
     */
    public function mentorshipSession(): BelongsToMany {
        return $this->belongsToMany(MentorshipSessionHistory::class, 'status_id', 'id');
    }
}
