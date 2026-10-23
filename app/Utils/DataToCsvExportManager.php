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

class DataToCsvExportManager {
    private $fileName;

    private $storageLayerClass;

    private $columnsWithPossiblyMultipleValues;

    public function __construct($fileName, $storageLayerClass, array $columnsWithPossiblyMultipleValues = [], $lang = 'en') {
        $this->fileName = $fileName;
        $this->storageLayerClass = $storageLayerClass;
        // create fields that will hold the multiple values fetched from DB 'left joins'
        foreach ($columnsWithPossiblyMultipleValues as $columnWithPossiblyMultipleValues) {
            $this->$columnWithPossiblyMultipleValues = [];
        }
        $this->columnsWithPossiblyMultipleValues = $columnsWithPossiblyMultipleValues;
        App::setLocale($lang);
    }

    public function getExportedData() {
        $dataForExportation = (new $this->storageLayerClass)->getDataForExportation();
        if (!empty($dataForExportation)) {
            $csv = $this->getCsvFromData($dataForExportation);

            return response($csv)
                ->header('Content-Type', 'application/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $this->fileName . '"')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } else {
            return 'NO_DATA_FOUND';
        }
    }

    private function getCsvFromData($dataForExportation) {
        $p = 'export_columns.';
        // insert column titles in csv
        $keys = array_keys(get_object_vars($dataForExportation[0]));
        // the variable that stores all the returned information
        $exportData = '';
        foreach ($keys as $key) {
            $exportData .= '"' . Lang::get($p . $key) . '";';
        }
        // replace last ';' with '\n' (change row and not just column in .csv)
        $exportData = substr_replace($exportData, "\n", -1);
        // export data
        $lastExportedId = -1;
        foreach ($dataForExportation as $rowKey => $rowForExportation) {
            // the temporary variable used to store the row's info and concatenate the $exportData variable if it should
            $tempExportData = '';
            // clear columns with possibly multiple values if the new row's id
            // is different from the last exported row's id
            if ($rowForExportation->id !== $lastExportedId) {
                foreach ($this->columnsWithPossiblyMultipleValues as $columnWithPossiblyMultipleValues) {
                    $this->$columnWithPossiblyMultipleValues = [];
                }
            }
            foreach ($keys as $key) {
                // check if column has possibility of multiple values
                if ($this->hasColumnPossibilityOfMultipleValues($key)) {
                    // check if array item exists, else push to array with all values for column
                    if (!in_array($rowForExportation->$key, $this->$key)) {
                        array_push($this->$key, $rowForExportation->$key);
                    }
                    $tempExportData .= $this->cell(implode(',', $this->$key));
                } else {
                    $tempExportData .= $this->cell($rowForExportation->$key);
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

    /**
     * One quoted CSV cell. A value from the public forms that starts like a
     * formula (=, +, -, @, tab, carriage return) gets a leading apostrophe, so
     * a spreadsheet shows it as text instead of running it. Numbers such as
     * +30 210 1234567 cannot run and stay as they are.
     */
    private function cell(mixed $value): string {
        $text = str_replace('"', "'", (string) $value);
        $isNumber = preg_match('/^[+-]?[\d\s().-]+$/', $text) === 1;
        if ($text !== '' && !$isNumber && in_array($text[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            $text = "'" . $text;
        }

        return '"' . $text . '";';
    }

    private function hasColumnPossibilityOfMultipleValues($columnName) {
        return in_array($columnName, $this->columnsWithPossiblyMultipleValues);
    }
}
