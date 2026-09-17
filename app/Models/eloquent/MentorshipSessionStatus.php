<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MentorshipSessionStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentorship_session_status_lookup';

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\MentorshipSessionHistory, $this>
     */
    public function mentorshipSession(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MentorshipSessionHistory::class, 'status_id', 'id');
    }

}
