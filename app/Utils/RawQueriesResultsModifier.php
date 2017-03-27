<?php

/**
 * Created by PhpStorm.
 * User: snik
 * Date: 3/27/17
 * Time: 2:40 PM
 */

namespace  App\Utils;

class RawQueriesResultsModifier
{
    public static function transformRawQueryStorageResultsToOneDimensionalArray($results) {
        $temp = array();
        foreach ($results as $result) {
            array_push($temp, $result->id);
        }
        return $temp;
    }
}
