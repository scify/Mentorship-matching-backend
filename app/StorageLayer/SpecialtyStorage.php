<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 7/2/2017
 * Time: 12:57 μμ
 */

namespace App\StorageLayer;


use App\Models\eloquent\MentorSpecialty;
use App\Models\eloquent\Specialty;

class SpecialtyStorage {

    public function getAllSpecialties() {
        return Specialty::orderBy('name')->get();
    }

    public function getPublicSpecialties() {
        return Specialty::where('status_id', 1)->orderBy('name')->get();
    }

    public function getSpecialtyById($id) {
        return Specialty::find($id);
    }

    public function saveSpecialty(MentorSpecialty $newMentorSpecialty) {
        $newMentorSpecialty->save();
        return $newMentorSpecialty;
    }

    public function getSpecialtyForMentor($mentorProfileId, $specialtyId) {
        return MentorSpecialty::where(['mentor_profile_id' => $mentorProfileId, 'specialty_id' => $specialtyId])->firstOrFail();
    }

    public function createSpecialty($specialtyName) {
        $specialty = new Specialty();
        $specialty->name = $specialtyName;
        $specialty->status_id = 2; // all newly created specialties will be private
        $specialty->save();
        return $specialty;
    }
}
