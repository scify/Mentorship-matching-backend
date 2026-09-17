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
     * @var list<string>
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\Company, $this>
     */
    public function company(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\User, $this>
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'creator_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\Reference, $this>
     */
    public function reference(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Reference::class, 'id', 'reference_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\Residence, $this>
     */
    public function residence(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Residence::class, 'id', 'residence_id');
    }

    /**
     * Get the mentor's specialties
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\Specialty, $this>
     */
    public function specialties(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'mentor_specialty')->wherePivot('deleted_at', null);
    }

    /**
     * Get the mentor's additional specialties
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\eloquent\Industry, $this>
     */
    public function industries(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Industry::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\MentorStatus, $this>
     */
    public function status(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MentorStatus::class, 'id', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MentorStatusHistory, $this>
     */
    public function statusHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MentorStatusHistory::class, 'mentor_profile_id', 'id')->orderBy('created_at', 'desc');
    }

    public function hasCompany(){

        return (bool) $this->company()->first();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MentorshipSession, $this> */
    public function sessions(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\University, $this>
     */
    public function university(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(University::class, 'id', 'university_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\EducationLevel, $this>
     */
    public function educationLevel(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MentorRating, $this>
     */
    public function ratings(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(MentorRating::class, 'mentor_id', 'id');
    }
}
