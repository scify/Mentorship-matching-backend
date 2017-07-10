<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/24/17
 * Time: 11:10 AM
 */

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\MenteeProfile;
use App\Models\eloquent\Residence;
use App\Models\eloquent\Specialty;
use App\Models\eloquent\University;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use parseCSV;

class UpdateSpecialtyForOldMentees
{
    private $filePath;

    // fields from excel file
    private $fileImportFields = [
        'first_name_low', 'first_name', 'last_name_low', 'last_name', 'age', 'university_department_name',
        'university', 'university_graduation_year_with_zero_values', 'specialty', 'specialty_experience', 'expectations',
        'career_goals', 'linkedin_url', 'is_employed', 'job_description', 'reference_text',
        'address', 'residence', 'email', 'phone', 'cell_phone', 'registered'
    ];

    public function __construct()
    {
        $this->filePath = env('MENTEES_EXCEL_FILE_PATH', '../resources/excel/mentees.csv');
    }

    private function translateCSVColumnsToExpectedDBNames($colValues) {
        $tempCounter = 0;
        $newColValues = array();
        foreach ($colValues as $key => $value) {
            if ($tempCounter < count($this->fileImportFields)) {
                $newColValues[$this->fileImportFields[$tempCounter]] = $value;
                $tempCounter++;
            }
        }
        return $newColValues;
    }

    // inserts all values from excel to DB
    public function updateMentees()
    {
        header('Content-Type: text/html; charset=UTF-8');
        // import csv
        $csvFile = new parseCSV($this->filePath);
        // iterate between all rows of the csv file and add each value to the appropriate table
        for($i = 0; $i < count($csvFile->data); $i++) {
            $colValues = $this->translateCSVColumnsToExpectedDBNames($csvFile->data[$i]);
            $specialtyId = null;
            $mentee = null;
            for ($j = 0; $j < count($colValues); $j++) {
                // remove '\n' and trim column value
                $colContent = trim($colValues[$this->fileImportFields[$j]]);
                switch ($this->fileImportFields[$j]) {
                    case 'email':
                        // keep temporarily the age as year_of_birth (will be transformed later)
                        $mentee = MenteeProfile::where('email', $colContent)->first();
                        break;
                    case 'specialty':
                        if (!empty($colContent)) {
                            // get all comma separated specialties, rename to 'Marketing' if needed and push
                            // the value's DB id to specialty_id column
                            $specialty_pairings = explode(",", $colContent);
                            foreach ($specialty_pairings as $specialty_pairing) {
                                if ($specialty_pairing === 'Marketing / Πωλήσεις') {
                                    $specialty_pairing = 'Marketing';
                                }
                                $specialty = Specialty::where('name', trim($specialty_pairing))->first();
                                if (!empty($specialty))
                                    $specialtyId = $specialty->id;
                            }
                        }
                        break;
                }
                if ($mentee !== null and $specialtyId !== null) {
                    $mentee->specialty_id = $specialtyId;
                    $mentee->save();
                }
            }
        }
    }
}
