<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class UserIcon extends Model
{

    protected $table = 'user_icon';

    protected $fillable = ['id', 'title', 'path', 'description'];

}
