<?php

namespace App\StorageLayer;

use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\MentorProfile;
use Illuminate\Support\Facades\DB;

/**
 * Class MentorProfileStorage
 * @package app\StorageLayer
 *
 * Contains the eloquent queries methods for the MentorProfiles.
 */
class MentorStorage {

    public function saveMentor(MentorProfile $mentor) {
        $mentor->save();
        return $mentor;
    }

    public function getAllMentorProfiles() {
        return MentorProfile::orderBy('created_at')->get();
    }

    public function getMentorProfileById($id) {
        return MentorProfile::find($id);
    }

    public function getMentorsByCompanyId($companyId) {
        return MentorProfile::where(['company_id' => $companyId])
            ->orderBy('created_at')->get();
    }

    public function getMentorsThatMatchGivenNameOrEmail($searchQuery) {
        return MentorProfile::where('first_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('email', 'like', '%' . $searchQuery . '%')
            ->orderBy('created_at')->get();
    }

    public function getMentorsFromIdsArray($filteredMentorIds) {
        return MentorProfile::whereIn('id', $filteredMentorIds)
            ->orderBy('created_at')->get();
    }

    public function deleteMentor($mentor) {
        $mentor->delete();
    }

    public function getMentorProfilesWithStatusId($statusId) {
        return MentorProfile::where(['status_id' => $statusId])
            ->orderBy('created_at')->get();
    }

    public function getDataForExportation() {
        return DB::select(DB::raw('select mentor.id, mentor.first_name, mentor.last_name, mentor.year_of_birth, 
          mentor.address, mentor.email, mentor.linkedin_url, mentor.phone, mentor.cell_phone, 
          mentor.company_sector, mentor.job_position, mentor.job_experience_years, 
          mentor.skills, company.name as company_name, reference.name as reference_name,
          mentor.reference_text, specialty.name as specialty_name, industry.name as industry_name, 
          residence.name as residence_name_foreign, mentor.residence_name, 
          university.name as university_name_foreign, mentor.university_name, mentor.university_department_name, 
          education_level.name as education_level_name
          from mentor_profile as mentor 
          left join company on mentor.company_id = company.id left join reference on mentor.reference_id = reference.id
          left join mentor_specialty on mentor.id = mentor_specialty.mentor_profile_id
          left join specialty on mentor_specialty.specialty_id = specialty.id left join mentor_industry on mentor.id = mentor_industry.mentor_profile_id
          left join industry on mentor_industry.industry_id = industry.id left join residence on mentor.residence_id = residence.id
          left join university on mentor.university_id = university.id left join education_level on mentor.education_level_id = education_level.id
          where mentor.deleted_at is null
          order by mentor.id'));
    }
}
