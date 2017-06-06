<?php
/**
 * Created by PhpStorm.
 * User: snik
 * Date: 6/6/17
 * Time: 11:48 AM
 */

namespace App\Utils;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class DataToCsvExportManager
{
    private $fileName;

    private $storageLayerClass;

    private $columnsWithPossiblyMultipleValues;

    public function __construct($fileName = 'export.csv', $storageLayerClass, array $columnsWithPossiblyMultipleValues = [], $lang = 'en') {
        $this->fileName = $fileName;
        $this->storageLayerClass = $storageLayerClass;
        // create fields that will hold the multiple values fetched from DB 'left joins'
        foreach ($columnsWithPossiblyMultipleValues as $columnWithPossiblyMultipleValues) {
            $this->$columnWithPossiblyMultipleValues = array();
        }
        $this->columnsWithPossiblyMultipleValues = $columnsWithPossiblyMultipleValues;
        App::setLocale($lang);
    }

    public function getExportedData() {
        $dataForExportation = (new $this->storageLayerClass)->getDataForExportation();
        if(!empty($dataForExportation)) {
            $csv = $this->getCsvFromData($dataForExportation);
            return response($csv)
                ->header('Content-Type', 'application/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $this->fileName . '"')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } else {
            return "NO_DATA_FOUND";
        }
    }

    private function getCsvFromData($dataForExportation) {
        $p = "export_columns.";
        // insert column titles in csv
        $keys = array_keys(get_object_vars($dataForExportation[0]));
        // the variable that stores all the returned information
        $exportData = "";
        foreach($keys as $key) {
            $exportData .= Lang::get($p . $key) . ";";
        }
        // replace last ';' with '\n' (change row and not just column in .csv)
        $exportData = substr_replace($exportData, "\n", -1);
        // export data
        $lastExportedId = -1;
        foreach ($dataForExportation as $rowKey => $rowForExportation) {
            // the temporary variable used to store the row's info and concatenate the $exportData variable if it should
            $tempExportData = "";
            // clear columns with possibly multiple values if the new row's id
            // is different from the last exported row's id
            if ($rowForExportation->id !== $lastExportedId) {
                foreach ($this->columnsWithPossiblyMultipleValues as $columnWithPossiblyMultipleValues) {
                    $this->$columnWithPossiblyMultipleValues = array();
                }
            }
            foreach ($keys as $key) {
                // check if column has possibility of multiple values
                if ($this->hasColumnPossibilityOfMultipleValues($key)) {
                    // check if array item exists, else push to array with all values for column
                    if (!in_array($rowForExportation->$key, $this->$key)) {
                        array_push($this->$key, $rowForExportation->$key);
                    }
                    $tempExportData .= "\"" . implode(",", $this->$key) . "\";";
                } else {
                    $tempExportData .= "\"" . $rowForExportation->$key . "\";";
                }
            }
            // if it is the last row or the next row's id is different from current row's id concatenate
            // $exportData with $tempExportData
            if ($rowKey === count($dataForExportation) - 1 || $dataForExportation[$rowKey + 1]->id !== $rowForExportation->id) {
                $tempExportData = substr_replace($tempExportData, "\n", -1);
                $exportData .= $tempExportData;
            }
            $lastExportedId = $rowForExportation->id;
        }
        return $exportData;
    }

    private function hasColumnPossibilityOfMultipleValues($columnName) {
        return in_array($columnName, $this->columnsWithPossiblyMultipleValues);
    }
}
