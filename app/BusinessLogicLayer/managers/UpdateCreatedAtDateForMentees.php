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

class UpdateCreatedAtDateForMentees
{
    private $filePath;

    // fields from excel file
    private $fileImportFields = [
        'last_name', 'first_name', 'age', 'university_department_name',
        'university', 'university_graduation_year_with_zero_values', 'specialty', 'specialty_experience', 'expectations',
        'career_goals', 'linkedin_url', 'is_employed', 'job_description', 'reference_text',
        'address', 'residence', 'email', 'phone', 'cell_phone', 'registered'
    ];

    public function __construct()
    {
        $this->filePath = '../resources/excel/mentees.csv';
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
    public function fileUpdateMentees()
    {
        header('Content-Type: text/html; charset=UTF-8');
        // import csv
        $csvFile = new parseCSV($this->filePath);
        $conditionIsTriggered = false;
        // iterate between all rows of the csv file
        for($i = 0; $i < count($csvFile->data); $i++) {
            $colValues = $this->translateCSVColumnsToExpectedDBNames($csvFile->data[$i]);
            $email = '';
            for($j = 0; $j < count($colValues); $j++) {
                // remove '\n' and trim column value
                $colContent = trim($colValues[$this->fileImportFields[$j]]);
                if ($this->fileImportFields[$j] === 'email') {
                    $email = $colContent;
                    // all rows in mentees.csv should be updated after this row with that email
                    if ($colContent === 'mariosandres90@gmail.com')
                        $conditionIsTriggered = true;
                }
                if ($conditionIsTriggered && $this->fileImportFields[$j] === 'registered') {
                    if (!empty($colContent)) {
                        $createdAt = Carbon::createFromFormat("m/d/Y", $colContent);
                        $mentee = MenteeProfile::where('email', $email)->first();
                        $mentee->timestamps = false;
                        $mentee->created_at = $createdAt;
                        $mentee->save();
                    }
                }
            }
        }
    }
}
