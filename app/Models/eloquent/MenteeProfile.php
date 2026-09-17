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
     * @var list<string>
     */
    protected $fillable = ['first_name', 'last_name', 'year_of_birth', 'status_id', 'address',
        'residence_id', 'residence_name', 'email', 'linkedin_url', 'phone', 'cell_phone',
        'education_level_id', 'university_id', 'university_name', 'university_department_name', 'university_graduation_year',
        'is_employed', 'job_description', 'specialty_experience',
        'expectations', 'career_goals', 'reference_id', 'reference_text', 'creator_user_id', 'skills', 'cv_file_name'
    ];

    protected $with = ['ratings'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\Residence, $this>
     */
    public function residence(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(Residence::class, 'id', 'residence_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\Reference, $this>
     */
    public function reference(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(Reference::class, 'id', 'reference_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\eloquent\User, $this>
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(User::class, 'id', 'creator_user_id');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MentorshipSession, $this>
     */
    public function sessions(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\eloquent\MenteeStatusHistory, $this>
     */
    public function statusHistory(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(MenteeStatusHistory::class, 'mentee_profile_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * @return HasMany<MenteeRating, $this>
     */
    public function ratings(): HasMany {
        return $this->hasMany(MenteeRating::class, 'mentee_id', 'id');
    }

    /**
     * Get the mentee's specialties
     *
     * @return BelongsToMany<Specialty, $this>
     */
    public function specialties(): BelongsToMany {
        return $this->belongsToMany(Specialty::class, 'mentee_specialty')->wherePivot('deleted_at', null);
    }
}
