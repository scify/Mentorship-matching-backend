<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/9/17
 * Time: 3:11 PM
 */

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorshipSession extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentorship_session';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mentor_profile_id', 'mentee_profile_id', 'account_manager_id', 'matcher_id', 'status_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mentor() {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mentee() {
        return $this->hasOne(MenteeProfile::class, 'id', 'mentee_profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account_manager() {
        return $this->hasOne(User::class, 'id', 'account_manager_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function matcher() {
        return $this->hasOne(User::class, 'id', 'matcher_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() {
        return $this->hasOne(MentorshipSessionStatus::class, 'id', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history() {
        return $this->hasMany(MentorshipSessionHistory::class, 'mentorship_session_id', 'id');
    }
}
