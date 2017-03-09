<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\CompanyManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\Models\eloquent\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    private $companyManager;
    private $mentorManager;
    private $userManager;

    public function __construct() {
        $this->companyManager = new CompanyManager();
        $this->mentorManager = new MentorManager();
        $this->userManager = new UserManager();
    }

    /**
     * Display all companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllCompanies()
    {
        $companies = $this->companyManager->getAllCompanies();
        $loggedInUser = Auth::user();
        return view('companies.list_all', [
            'pageTitle'=>'All Companies',
            'companies' => $companies,
            'loggedInUser' => $loggedInUser
        ]);
    }

    /**
     * Show the form for creating a new company.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateForm()
    {
        $company = new Company();
        $formTitle = 'FILL IN THE FORM TO REGISTER A NEW COMPANY';
        $mentors = $this->mentorManager->getMentorsWithNoCompanyAssigned();
        $companyMentorsIds = array();
        $accountManagers = $this->userManager->getAccountManagersWithNoCompanyAssigned();
        return view('companies.forms.create_edit', [
            'pageTitle' => 'Company',
            'pageSubTitle' => 'create new company',
            'company' => $company,
            'formTitle' => $formTitle, 'mentors' => $mentors, 'companyMentorsIds' => $companyMentorsIds,
            'accountManagers' => $accountManagers
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEditForm($id)
    {
        $company = $this->companyManager->getCompany($id);
        $mentors = $this->mentorManager->getMentorsWithNoCompanyAssignedExceptCompany($company);
        $companyMentorsIds = $this->companyManager->getCompanyMentorsIds($company);
        $accountManagers = $this->userManager->getAccountManagersWithNoCompanyAssignedExceptCurrent($company);
        $formTitle = 'EDIT COMPANY';
        return view('companies.forms.create_edit', [
            'pageTitle' => 'Company', 'pageSubTitle' => 'edit company', 'company' => $company,
            'formTitle' => $formTitle, 'mentors' => $mentors, 'companyMentorsIds' => $companyMentorsIds,
            'accountManagers' => $accountManagers
        ]);
    }

    /**
     * Store a newly created company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $input = $request->all();

        try {
            $this->companyManager->createCompany($input);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Company created');
        return $this->showAllCompanies();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $input = $request->all();
        try {
            $this->companyManager->editCompany($input, $id);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Company edited');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $input = $request->all();
        $companyId = $input['company_id'];
        if($companyId == null || $companyId == "") {
            session()->flash('flash_message_failure', 'Something went wrong. Please try again.');
            return back();
        }
        try {
            $this->companyManager->deleteCompany($companyId);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back();
        }
        session()->flash('flash_message_success', 'Company deleted');
        return back();
    }
}
