<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['id', 'title', 'description'];

    /**
     * Get the @see User instances for this role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\User, $this>
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role')->wherePivot('deleted_at', null);
    }
}
