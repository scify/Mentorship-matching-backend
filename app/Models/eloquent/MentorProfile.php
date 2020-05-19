<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MentorProfile extends Model
{
    use SoftDeletes, Notifiable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mentor_profile';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'year_of_birth', 'address',
        'residence_id', 'residence_name', 'email', 'linkedin_url', 'phone', 'cell_phone',
        'company_name', 'company_sector', 'job_position', 'job_experience_years',
        'education_level_id', 'university_id',
        'university_name', 'university_department_name', 'skills', 'cv_file_name', 'reference_id',
        'reference_text', 'status_id', 'company_id', 'creator_user_id'
    ];

    protected $with = ['ratings'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
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
    public function reference()
    {
        return $this->hasOne(Reference::class, 'id', 'reference_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function residence()
    {
        return $this->hasOne(Residence::class, 'id', 'residence_id');
    }

    /**
     * Get the mentor's specialties
     */
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'mentor_specialty')->wherePivot('deleted_at', null);
    }

    /**
     * Get the mentor's additional specialties
     */
    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(MentorStatus::class, 'id', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusHistory()
    {
        return $this->hasMany(MentorStatusHistory::class, 'mentor_profile_id', 'id')->orderBy('created_at', 'desc');
    }

    public function hasCompany(){

        return (bool) $this->company()->first();
    }

    public function sessions() {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function university() {
        return $this->hasOne(University::class, 'id', 'university_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function educationLevel() {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings() {
        return $this->hasMany(MentorRating::class, 'mentor_id', 'id');
    }
}
