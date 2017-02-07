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
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentors()
    {
        return $this->belongsTo(MentorProfile::class, 'residence_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentees()
    {
        return $this->belongsTo(MenteeProfile::class, 'residence_id', 'id');
    }
}
