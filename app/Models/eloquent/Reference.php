<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reference extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reference';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name'];

    /**
     * @return BelongsTo<MenteeProfile, $this>
     */
    public function mentees(): BelongsTo {
        return $this->belongsTo(MenteeProfile::class, 'reference_id', 'id');
    }

    /**
     * @return BelongsTo<MentorProfile, $this>
     */
    public function mentors(): BelongsTo {
        return $this->belongsTo(MentorProfile::class, 'reference_id', 'id');
    }
}
