<?php

namespace App\StorageLayer;

use App\Models\eloquent\MentorProfile;

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
}
