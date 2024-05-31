<?php

namespace App\StorageLayer;

use App\BusinessLogicLayer\enums\MenteeStatuses;
use App\Models\eloquent\MenteeProfile;
use Illuminate\Support\Facades\DB;

/**
 * Class MenteeStorage
 * @package App\StorageLayer
 *
 * Contains the eloquent queries methods for the MenteeProfiles.
 */
class MenteeStorage {

    public function saveMentee(MenteeProfile $mentee) {
        $mentee->save();
        return $mentee;
    }

    public function getAllMenteeProfiles() {
        return MenteeProfile::withCount(['sessions as numberOfTotalSessions'])->orderBy('created_at')->get();
    }

    public function getMenteeProfileById($id) {
        return MenteeProfile::find($id);
    }

    public function getMenteeProfileByEmail($email) {
        return MenteeProfile::where('email', $email)->first();
    }

    public function getMenteesThatMatchGivenNameOrEmail($searchQuery) {
        return MenteeProfile::where('first_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
            ->orWhere('email', 'like', '%' . $searchQuery . '%')->orderBy('created_at')->get();
    }

    public function getMenteesFromIdsArray($filteredMenteeIds) {
        return MenteeProfile::whereIn('id', $filteredMenteeIds)->orderBy('created_at')->get();
    }

    public function getMenteeProfilesWithStatusId($statusId) {
        return MenteeProfile::where(['status_id' => $statusId])
            ->orderBy('created_at')->get();
    }

    public function getDataForExportation() {
        return DB::select(DB::raw('select mentee.id, mentee.first_name, mentee.last_name, mentee.year_of_birth, 
          mentee.address, mentee.email, mentee.linkedin_url, mentee.phone, mentee.cell_phone, 
          mentee.job_description, mentee.is_employed, mentee.expectations, mentee.career_goals, mentee.skills, reference.name as reference_name,
          mentee.reference_text, specialty.name as specialty_name, mentee.specialty_experience, 
          residence.name as residence_name_foreign, mentee.residence_name, 
          university.name as university_name_foreign, mentee.university_name, mentee.university_department_name, 
          mentee.university_graduation_year, education_level.name as education_level_name, mentee_status_lookup.description as status
          from mentee_profile as mentee
          left join mentee_specialty on mentee.id = mentee_specialty.mentee_profile_id  
          left join specialty on mentee_specialty.specialty_id = specialty.id     
          left join university on mentee.university_id = university.id 
          left join education_level on mentee.education_level_id = education_level.id
          left join residence on mentee.residence_id = residence.id
          left join reference on mentee.reference_id = reference.id
          left join mentee_status_lookup on mentee.status_id = mentee_status_lookup.id
          where mentee.deleted_at is null
          order by mentee.id'));
    }

    public function getUnmatchedMenteesCreatedSince($months) {
        return MenteeProfile::where('status_id', MenteeStatuses::$statuses['available'])
            ->whereDoesntHave('sessions')
            ->whereRaw('created_at < DATE_SUB(NOW(), INTERVAL ' . $months . ' MONTH)')
            ->orderBy('created_at')->limit(1)->get();
    }
}
