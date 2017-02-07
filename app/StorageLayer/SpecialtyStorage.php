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
        return Specialty::all();
    }

    public function getSpecialtyById($id) {
        return Specialty::find($id);
    }

    public function saveSpecialty(MentorSpecialty $newMentorSpecialty) {
        $newMentorSpecialty->save();
        return $newMentorSpecialty;
    }
}