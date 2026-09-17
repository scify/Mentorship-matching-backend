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
     * @var list<string>
     */
    protected $fillable = ['mentor_profile_id', 'mentee_profile_id', 'account_manager_id', 'matcher_id', 'status_id', 'general_comment', 'deleted_at'];

    protected $with = ['mentor', 'mentee', 'account_manager', 'matcher', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorProfile, $this>
     */
    public function mentor(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(MentorProfile::class, 'id', 'mentor_profile_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MenteeProfile, $this>
     */
    public function mentee(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(MenteeProfile::class, 'id', 'mentee_profile_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\User, $this>
     */
    public function account_manager(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(User::class, 'id', 'account_manager_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\User, $this>
     */
    public function matcher(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(User::class, 'id', 'matcher_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorshipSessionStatus, $this>
     */
    public function status(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(MentorshipSessionStatus::class, 'id', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MentorshipSessionHistory, $this>
     */
    public function history(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(MentorshipSessionHistory::class, 'mentorship_session_id', 'id')->orderBy('updated_at', 'desc');
    }
}
