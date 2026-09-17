<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class UserState extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_state';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['id', 'title', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\eloquent\User', 'role_id', 'id');
    }
}
