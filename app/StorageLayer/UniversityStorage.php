<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/22/17
 * Time: 12:43 PM
 */

namespace App\StorageLayer;

use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\University;
use Illuminate\Support\Collection;

class UniversityStorage
{
    public function getAllUniversities() {
        $universities = University::where('name', '<>', 'Άλλο')->orderBy('name')->get();
        $otherUniversity = University::where('name', 'Άλλο')->first();
        $universities->push($otherUniversity);
        return $universities;
    }

    public function getAllUniversitiesIncludingOtherUniversities() {
        $universities = University::select('id', 'name')->where('name', '<>', 'Άλλο')->get();
        $allUniversities = new Collection();
        // get all university names declared by mentees
        $menteesWithOtherUniversities = MenteeProfile::select('university_name')->distinct()->where('university_name', '<>', null)->get();
        foreach ($menteesWithOtherUniversities as $menteeWithOtherUniversities) {
            $allUniversities->push(new University([
                'id' => $menteeWithOtherUniversities->university_name,
                'name' => $menteeWithOtherUniversities->university_name
            ]));
        }
        // push all DB universities to the same collection
        foreach ($universities as $university) {
            $allUniversities->push($university);
        }
        // sort by name and add the 'other' choice at the bottom
        $allUniversities = $allUniversities->sortBy('name');
        $otherUniversity = University::select('id', 'name')->where('name', 'Άλλο')->first();
        $allUniversities->push($otherUniversity);
        return $allUniversities;
    }
}
