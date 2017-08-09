<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenteeSpecialty extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentee_specialty';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mentee_profile_id', 'specialty_id'];
}
