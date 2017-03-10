<?php

namespace App\StorageLayer;

class RawQueryStorage {

    public function performRawQuery($query) {
        return \DB::select(\DB::raw($query));
    }
}
