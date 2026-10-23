<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserState extends Model {
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
     * @return BelongsTo
     */
    public function users(): BelongsTo {
        return $this->belongsTo('App\Models\eloquent\User', 'role_id', 'id');
    }
}
