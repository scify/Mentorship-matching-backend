<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class MentorStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_status_lookup';

    protected $fillable = ['status', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentors(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MentorProfile::class, 'status_id', 'id');
    }

}
