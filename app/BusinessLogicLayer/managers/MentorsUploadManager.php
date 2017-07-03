<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 5/24/17
 * Time: 11:10 AM
 */

namespace App\BusinessLogicLayer\managers;

use App\Models\eloquent\Company;
use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\MentorSpecialty;
use App\Models\eloquent\Residence;
use App\Models\eloquent\Specialty;
use App\Models\eloquent\University;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use parseCSV;

class MentorsUploadManager
{
    private $filePath;

    // fields from excel file
    private $fileImportFields = [
        'last_name_low', 'last_name', 'first_name_low', 'first_name', 'age', 'company_name', 'company_sector',
        'job_position', 'job_experience_years', 'specialty_pairing', 'specialty', 'skills',
        'address', 'residence', 'email', 'phone', 'cell_phone', 'linkedin_url', 'reference',
        'registered', 'university', 'university_department_name'
    ];

    // mentor model fields
    private $mentorModelFields = [
        'id', 'creator_user_id', 'reference_id', 'first_name', 'last_name', 'year_of_birth',
        'address', 'residence_id', 'email', 'linkedin_url', 'phone', 'cell_phone',
        'company_sector', 'job_position', 'job_experience_years', 'education_level_id',
        'university_id', 'university_name', 'university_department_name', 'skills',
        'cv_file_name', 'company_id', 'status_id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function __construct()
    {
        $this->filePath = env('MENTORS_EXCEL_FILE_PATH');
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
    public function fileImportMentors()
    {
        header('Content-Type: text/html; charset=UTF-8');
        // import csv
        $csvFile = new parseCSV($this->filePath);
        // iterate between all rows of the csv file and add each value to the appropriate table
        for($i = 0; $i < count($csvFile->data); $i++) {
            $colValues = $this->translateCSVColumnsToExpectedDBNames($csvFile->data[$i]);
            $mentor = new MentorProfile();
            // set default values to columns that are not included in the excel file
            $mentor->cv_file_name = null;
            $mentor->university_id = 12; // set to 'other' university as default
            $mentor->creator_user_id = Auth::id();
            $createdAt = new Carbon();
            $specialtiesIds = array();
            for($j = 1; $j < count($colValues); $j++) {
                // remove '\n' and trim column value
                $colContent = trim($colValues[$this->fileImportFields[$j]]);
                if (array_search($this->fileImportFields[$j], $this->mentorModelFields)) {
                    $fieldName = $this->fileImportFields[$j];
                    $mentor->$fieldName = $colContent;
                } else {
                    switch ($this->fileImportFields[$j]) {
                        case 'age':
                            // keep temporarily the age as year_of_birth (will be transformed later)
                            $mentor->year_of_birth = $colContent;
                            break;
                        case 'company_name':
                            if (!empty($colContent)) {
                                // for company_name, check if it already exists in DB, otherwise store it
                                $company = Company::where('name', $colContent)->first();
                                if (empty($company)) {
                                    $company = new Company();
                                    $company->name = $colContent;
                                    $company->save();
                                }
                                $mentor->company_id = $company->id;
                            }
                            break;
                        case 'specialty_pairing':
                            if (!empty($colContent)) {
                                // get all comma separated specialties, rename to 'Marketing' if needed and push
                                // the value's DB id to an array that holds all the selected specialties ids
                                $specialty_pairings = explode(",", $colContent);
                                $needToAddSalesSpecialtyId = false;
                                foreach ($specialty_pairings as $specialty_pairing) {
                                    if ($specialty_pairing === 'Marketing / Πωλήσεις') {
                                        $specialty_pairing = 'Marketing';
                                        $needToAddSalesSpecialtyId = true;
                                    }
                                    $specialty = Specialty::where('name', trim($specialty_pairing))->first();
                                    if(!empty($specialty))
                                        array_push($specialtiesIds, $specialty->id);
                                }
                                if ($needToAddSalesSpecialtyId)
                                    array_push($specialtiesIds, 10); // Sales specialty has id = 10
                            }
                            break;
                        case 'specialty':
                            // IGNORING specialty!!!
                            break;
                        case 'residence':
                            if (!empty($colContent)) {
                                $residence = Residence::where('name', $colContent)->first();
                                if (!empty($residence)) {
                                    $mentor->residence_id = $residence->id;
                                }
                            }
                            break;
                        case 'reference':
                            if (!empty($colContent)) {
                                $mentor->reference_text = $colContent;
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
                                    $mentor->university_id = $university->id;
                                } else {
                                    $mentor->university_id = 12; // set to 'other' university
                                    $mentor->university_name = $colContent;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
            $mentor->created_at = $createdAt;
            $mentor->updated_at = $createdAt;
            // transform age to year_of_birth
            $mentor->year_of_birth = (!empty($mentor->year_of_birth)) ? $createdAt->year - $mentor->year_of_birth : null;
            try {
                if (!empty($mentor->email)) {
                    $storedMentorWithSameEmail = MentorProfile::where('email', $mentor->email)->first();
                    if (empty($storedMentorWithSameEmail)) {
                        $mentor->save();
                        foreach ($specialtiesIds as $specialtyId) {
                            $mentorSpecialty = new MentorSpecialty();
                            $mentorSpecialty->mentor_profile_id = $mentor->id;
                            $mentorSpecialty->specialty_id = $specialtyId;
                            $mentorSpecialty->save();
                        }
                    } else
                        Log::info('Mentor with ' . $mentor->email . ' email already exists. Mentor will not be stored.');
                } else {
                    Log::info('Mentor in line ' . $i . ' couldn\'t be stored. Stopped at column ' . $j);
                }
            } catch (\Exception $e){
                Log::error('Mentor in line ' . $i . ' couldn\'t be stored due to error: ' . $e->getMessage());
            }
        }
    }
}
