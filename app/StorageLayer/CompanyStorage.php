<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 9/2/2017
 * Time: 2:06 μμ
 */

namespace app\StorageLayer;

use App\Models\eloquent\Company;

class CompanyStorage {
    public function saveCompany(Company $company) {
        $company->save();
        return $company;
    }

    public function getAllCompanies() {
        return Company::orderBy('name')->get();
    }

    public function getCompanyById($id) {
        return Company::find($id);
    }

    public function getCompanyByName($name) {
        return Company::where('name', $name)->first();
    }

    public function getCompaniesByAccountManagerId($accountManagerId) {
        return Company::where(['account_manager_id' => $accountManagerId])->get();
    }
}
