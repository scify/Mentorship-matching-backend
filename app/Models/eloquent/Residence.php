<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Residence extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'residence';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name'];

    /**
     * @return BelongsTo<MentorProfile, $this>
     */
    public function mentors(): BelongsTo {
        return $this->belongsTo(MentorProfile::class, 'residence_id', 'id');
    }

    /**
     * @return BelongsTo<MenteeProfile, $this>
     */
    public function mentees(): BelongsTo {
        return $this->belongsTo(MenteeProfile::class, 'residence_id', 'id');
    }
}
