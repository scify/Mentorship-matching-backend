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
    protected $fillable = ['mentor_profile_id', 'mentee_profile_id', 'account_manager_id', 'matcher_id'];

//    public function mentor() {
//        return $this->hasOne(MentorProfile::class, 'id', 'mentor_profile_id');
//    }
//
//    public function mentee() {
//        return $this->hasOne(MenteeProfile::class, 'id', 'mentee_profile_id');
//    }
//
//    public function account_manager() {
//        return $this->hasOne(User::class, 'id', 'account_manager_id');
//    }
//
//    public function matcher() {
//        return $this->hasOne(User::class, 'id', 'matcher_id');
//    }
}
