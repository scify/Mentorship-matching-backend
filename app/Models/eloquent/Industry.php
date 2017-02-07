<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'industry';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
