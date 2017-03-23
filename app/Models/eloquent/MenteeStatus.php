<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MenteeStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentee_status_lookup';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentees()
    {
        return $this->belongsTo(MenteeProfile::class, 'status_id', 'id');
    }

}
