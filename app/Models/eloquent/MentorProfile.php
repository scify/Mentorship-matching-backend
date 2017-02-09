<?php

namespace App\Models\eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorProfile extends Model
{
    use SoftDeletes;
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
    protected $fillable = ['first_name', 'last_name', 'age', 'address',
        'residence_id', 'email', 'linkedin_url', 'phone', 'cell_phone',
        'company', 'company_sector', 'job_position', 'job_experience_years',
        'university_name', 'university_department_name', 'skills', 'reference',
        'is_available'
    ];

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
}
