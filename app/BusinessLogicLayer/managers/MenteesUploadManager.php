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

class MenteesUploadManager
{
    private $filePath;

    // fields from excel file
    private $fileImportFields = [
        'last_name', 'first_name', 'age', 'university_department_name',
        'university', 'university_graduation_year_with_zero_values', 'specialty', 'specialty_experience', 'expectations',
        'career_goals', 'linkedin_url', 'is_employed', 'job_description', 'reference_text',
        'address', 'residence', 'email', 'phone', 'cell_phone', 'registered'
    ];

    // mentee model fields
    private $menteeModelFields = [
        'id', 'creator_user_id', 'reference_id', 'reference_text', 'first_name', 'last_name', 'year_of_birth',
        'address', 'residence_id', 'email', 'linkedin_url', 'phone', 'cell_phone',
        'education_level_id', 'university_id', 'university_name', 'university_department_name',
        'university_graduation_year', 'is_employed', 'job_description', 'specialty_id',
        'specialty_experience', 'expectations', 'career_goals', 'skills', 'cv_file_name',
        'status_id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function __construct()
    {
        $this->filePath = env('MENTEES_EXCEL_FILE_PATH');
    }

    private function translateCSVColumnsToExpectedDBNames($colValues) {
        $tempCounter = 0;
        $newColValues = array();
        foreach ($colValues as $key => $value) {
            $newColValues[$this->fileImportFields[$tempCounter]] = $value;
            $tempCounter++;
        }
        return $newColValues;
    }

    // inserts all values from excel to DB
    public function fileImportMentees()
    {
        header('Content-Type: text/html; charset=UTF-8');
        // import csv
        $csvFile = new parseCSV($this->filePath);
        // iterate between all rows of the csv file and add each value to the appropriate table
        for($i = 0; $i < count($csvFile->data); $i++) {
            $colValues = $this->translateCSVColumnsToExpectedDBNames($csvFile->data[$i]);
            $mentee = new MenteeProfile();
            // set default values to columns that are not included in the excel file
            $mentee->cv_file_name = null;
            $mentee->university_id = 12; // set to 'other' university as default
            $mentee->reference_id = 7; // set to 'other' reference as default
            $mentee->creator_user_id = Auth::id();
            $createdAt = new Carbon();
            for($j = 0; $j < count($colValues); $j++) {
                // remove '\n' and trim column value
                $colContent = trim($colValues[$this->fileImportFields[$j]]);
                if (array_search($this->fileImportFields[$j], $this->menteeModelFields)) {
                    $fieldName = $this->fileImportFields[$j];
                    $mentee->$fieldName = $colContent;
                } else {
                    switch ($this->fileImportFields[$j]) {
                        case 'age':
                            // keep temporarily the age as year_of_birth (will be transformed later)
                            $mentee->year_of_birth = $colContent;
                            break;
                        case 'university_graduation_year_with_zero_values':
                            if ($colContent != 0) {
                                $mentee->university_graduation_year = $colContent;
                            }
                            break;
                        case 'specialty':
                            if (!empty($colContent)) {
                                if ($colContent === 'Marketing / Πωλήσεις') {
                                    $colContent = 'Marketing';
                                }
                                $specialty = Specialty::where('name', $colContent)->first();
                                if (!empty($specialty))
                                    $mentee->specialty_id = $specialty->id;
                            }
                            break;
                        case 'residence':
                            if (!empty($colContent)) {
                                $residence = Residence::where('name', $colContent)->first();
                                if (!empty($residence)) {
                                    $mentee->residence_id = $residence->id;
                                }
                            }
                            break;
                        case 'registered':
                            if (!empty($colContent)) {
                                $createdAt = Carbon::createFromFormat("d/m/Y", $colContent);
                            }
                            break;
                        case 'university':
                            if (!empty($colContent)) {
                                $university = University::where('name', $colContent)->first();
                                if (!empty($university)) {
                                    $mentee->university_id = $university->id;
                                } else {
                                    $mentee->university_id = 12; // set to 'other' university
                                    $mentee->university_name = $colContent;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
            $mentee->created_at = $createdAt;
            $mentee->updated_at = $createdAt;
            // transform age to year_of_birth
            $mentee->year_of_birth = (!empty($mentee->year_of_birth)) ? $createdAt->year - $mentee->year_of_birth : null;
            try {
                if (!empty($mentee->email)) {
                    $storedMenteeWithSameEmail = MenteeProfile::where('email', $mentee->email)->first();
                    if (empty($storedMenteeWithSameEmail)) {
                        $mentee->save();
                    } else
                        Log::info('Mentee with ' . $mentee->email . ' email already exists. Mentee will not be stored.');
                } else {
                    Log::info('Mentee in line ' . $i . ' couldn\'t be stored. Stopped at column ' . $j);
                }
            } catch (\Exception $e){
                Log::error('Mentee in line ' . $i . ' couldn\'t be stored due to error: ' . $e->getMessage());
            }
        }
    }
}
