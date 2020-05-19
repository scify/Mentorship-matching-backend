<?php


namespace App\Models\viewmodels;


class AllCompanyViewModels {
    public $companyViewModels;
    public $paginationObj;

    public function __construct($companyViewModels, $paginationObj) {
        $this->companyViewModels = $companyViewModels;
        $this->paginationObj = $paginationObj;
    }

}
