<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/22/17
 * Time: 12:43 PM
 */

namespace App\StorageLayer;

use App\Models\eloquent\University;

class UniversityStorage
{
    public function getAllUniversities() {
        $universities = University::where('name', '<>', 'Άλλο')->orderBy('name')->get();
        $otherUniversity = University::where('name', 'Άλλο')->first();
        $universities->push($otherUniversity);
        return $universities;
    }
}
