<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MenteeProfile extends Model
{
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
        'is_employed', 'job_description', 'specialty_id', 'specialty_experience',
        'expectations', 'career_goals', 'reference_id', 'reference_text', 'creator_user_id', 'skills', 'cv_file_name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function residence()
    {
        return $this->hasOne(Residence::class, 'id', 'residence_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reference()
    {
        return $this->hasOne(Reference::class, 'id', 'reference_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'creator_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function specialty()
    {
        return $this->hasOne(Specialty::class, 'id', 'specialty_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function university()
    {
        return $this->hasOne(University::class, 'id', 'university_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function educationLevel()
    {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions() {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusHistory()
    {
        return $this->hasMany(MenteeStatusHistory::class, 'mentee_profile_id', 'id')->orderBy('created_at', 'desc');
    }
}
