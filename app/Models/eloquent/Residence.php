<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentors(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MentorProfile::class, 'residence_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\eloquent\MenteeProfile, $this>
     */
    public function mentees(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenteeProfile::class, 'residence_id', 'id');
    }
}
