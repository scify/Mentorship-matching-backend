<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MentorProfile extends Model {
    use Notifiable, SoftDeletes;

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
        'reference_text', 'status_id', 'company_id', 'creator_user_id',
    ];

    protected $with = ['ratings'];

    /**
     * @return HasOne<Company, $this>
     */
    public function company(): HasOne {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    /**
     * @return HasOne<User, $this>
     */
    public function creator(): HasOne {
        return $this->hasOne(User::class, 'id', 'creator_user_id');
    }

    /**
     * @return HasOne<Reference, $this>
     */
    public function reference(): HasOne {
        return $this->hasOne(Reference::class, 'id', 'reference_id');
    }

    /**
     * @return HasOne<Residence, $this>
     */
    public function residence(): HasOne {
        return $this->hasOne(Residence::class, 'id', 'residence_id');
    }

    /**
     * Get the mentor's specialties
     *
     * @return BelongsToMany<Specialty, $this>
     */
    public function specialties(): BelongsToMany {
        return $this->belongsToMany(Specialty::class, 'mentor_specialty')->wherePivot('deleted_at', null);
    }

    /**
     * Get the mentor's additional specialties
     *
     * @return BelongsToMany<Industry, $this>
     */
    public function industries(): BelongsToMany {
        return $this->belongsToMany(Industry::class, 'mentor_industry')->wherePivot('deleted_at', null);
    }

    /**
     * @return HasOne<MentorStatus, $this>
     */
    public function status(): HasOne {
        return $this->hasOne(MentorStatus::class, 'id', 'status_id');
    }

    /**
     * @return HasMany<MentorStatusHistory, $this>
     */
    public function statusHistory(): HasMany {
        return $this->hasMany(MentorStatusHistory::class, 'mentor_profile_id', 'id')->orderBy('created_at', 'desc');
    }

    public function hasCompany() {
        return (bool) $this->company()->first();
    }

    /** @return HasMany<MentorshipSession, $this> */
    public function sessions(): HasMany {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * @return HasOne<University, $this>
     */
    public function university(): HasOne {
        return $this->hasOne(University::class, 'id', 'university_id');
    }

    /**
     * @return HasOne<EducationLevel, $this>
     */
    public function educationLevel(): HasOne {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    /**
     * @return HasMany<MentorRating, $this>
     */
    public function ratings(): HasMany {
        return $this->hasMany(MentorRating::class, 'mentor_id', 'id');
    }
}
