<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountManagerCapacity extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_manager_capacity';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['account_manager_id', 'capacity'];

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\User, $this> */
    public function accountManager(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(User::class, 'id', 'account_manager_id');
    }

}
