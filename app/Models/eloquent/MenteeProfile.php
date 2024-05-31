<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MenteeProfile extends Model {
    use SoftDeletes, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentee_profile';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'year_of_birth', 'status_id', 'address',
        'residence_id', 'residence_name', 'email', 'linkedin_url', 'phone', 'cell_phone',
        'education_level_id', 'university_id', 'university_name', 'university_department_name', 'university_graduation_year',
        'is_employed', 'job_description', 'specialty_experience',
        'expectations', 'career_goals', 'reference_id', 'reference_text', 'creator_user_id', 'skills', 'cv_file_name'
    ];

    protected $with = ['ratings'];


    /**
     * @return HasOne
     */
    public function residence() {
        return $this->hasOne(Residence::class, 'id', 'residence_id');
    }

    /**
     * @return HasOne
     */
    public function reference() {
        return $this->hasOne(Reference::class, 'id', 'reference_id');
    }

    /**
     * @return HasOne
     */
    public function creator() {
        return $this->hasOne(User::class, 'id', 'creator_user_id');
    }

    /**
     * @return HasOne
     */
    public function university() {
        return $this->hasOne(University::class, 'id', 'university_id');
    }

    /**
     * @return HasOne
     */
    public function educationLevel() {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    /**
     * @return HasMany
     */
    public function sessions() {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * @return HasMany
     */
    public function statusHistory() {
        return $this->hasMany(MenteeStatusHistory::class, 'mentee_profile_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * @return HasMany
     */
    public function ratings(): HasMany {
        return $this->hasMany(MenteeRating::class, 'mentee_id', 'id');
    }

    /**
     * Get the mentee's specialties
     */
    public function specialties(): BelongsToMany {
        return $this->belongsToMany(Specialty::class, 'mentee_specialty')->wherePivot('deleted_at', null);
    }
}
