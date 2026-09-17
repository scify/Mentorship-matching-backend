<?php

namespace App\StorageLayer;

use Illuminate\Support\Facades\DB;

class RawQueryStorage {

    /**
     * Runs a hand-written SELECT statement.
     *
     * The query MUST NOT contain interpolated user input. Every value that
     * originates from a request has to be passed as a `?` placeholder in
     * $query with its value in $bindings, so PDO sends it out-of-band
     * instead of it being parsed as SQL.
     *
     * @param string $query SQL with `?` placeholders for all values
     * @param array $bindings values for the placeholders, in order
     * @return array
     */
    public function performRawQuery(string $query, array $bindings = []) {
        return DB::select($query, $bindings);
    }

    /**
     * Builds a comma separated placeholder list (`?, ?, ?`) for an IN clause.
     *
     * @param array $values
     * @return string
     */
    public static function placeholdersFor(array $values): string {
        return implode(', ', array_fill(0, count($values), '?'));
    }
}
