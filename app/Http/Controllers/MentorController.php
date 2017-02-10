<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\CompanyManager;
use App\BusinessLogicLayer\managers\IndustryManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\ResidenceManager;
use App\BusinessLogicLayer\managers\SpecialtyManager;
use App\Models\eloquent\MentorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{

    private $mentorManager;
    private $specialtyManager;
    private $industryManager;
    private $residenceManager;

    public function __construct() {
        $this->specialtyManager = new SpecialtyManager();
        $this->industryManager = new IndustryManager();
        $this->mentorManager = new MentorManager();
        $this->residenceManager = new ResidenceManager();
    }

    /**
     * Display all mentors.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllMentors()
    {
        $mentors = $this->mentorManager->getAllMentors();
        $loggedInUser = Auth::user();
        return view('mentors.list_all', ['mentors' => $mentors, 'loggedInUser' => $loggedInUser]);
    }

    /**
     * Show the form for creating a new mentor.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateForm()
    {
        $companyManager = new CompanyManager();
        $mentor = new MentorProfile();
        $mentorSpecialtiesIds = array();
        $mentorIndustriesIds = array();
        $formTitle = 'Mentor registration';
        $specialties = $this->specialtyManager->getAllSpecialties();
        $industries = $this->industryManager->getAllIndustries();
        $residences = $this->residenceManager->getAllResidences();
        $companies = $companyManager->getAllCompanies();

        return view('mentors.forms.create_edit', ['mentor' => $mentor,
            'formTitle' => $formTitle, 'residences' => $residences,
            'specialties' => $specialties, 'industries' => $industries,
            'mentorSpecialtiesIds' => $mentorSpecialtiesIds,
            'mentorIndustriesIds' => $mentorIndustriesIds, 'loggedInUser' => Auth::user(),
            'companies' => $companies
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
        $companyManager = new CompanyManager();
        $mentor = $this->mentorManager->getMentor($id);
        $specialties = $this->specialtyManager->getAllSpecialties();
        $industries = $this->industryManager->getAllIndustries();
        $residences = $this->residenceManager->getAllResidences();
        $mentorSpecialtiesIds = $this->specialtyManager->getMentorSpecialtiesIds($mentor);
        $mentorIndustriesIds = $this->industryManager->getMentorIndustriesIds($mentor);
        $companies = $companyManager->getAllCompanies();
        $formTitle = 'Edit mentor';
        return view('mentors.forms.create_edit', ['mentor' => $mentor,
            'formTitle' => $formTitle, 'residences' => $residences,
            'specialties' => $specialties, 'industries' => $industries,
            'mentorSpecialtiesIds' => $mentorSpecialtiesIds,
            'mentorIndustriesIds' => $mentorIndustriesIds, 'loggedInUser' => Auth::user(),
            'companies' => $companies
        ]);
    }

    /**
     * Store a newly created mentor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'age' => 'required|numeric',
            'residence_id' => 'required',
            'address'        => 'required',
            'company' => 'required',
            'company_sector' => 'required',
            'job_position' => 'required',
            'job_experience_years' => 'required',
            'skills' => 'required',
            'specialties' => 'required',
            'industries' => 'required'
        ]);

        $input = $request->all();

        try {
            $this->mentorManager->createMentor($input);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Mentor created');
        return back();

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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'age' => 'required|numeric',
            'residence_id' => 'required',
            'address'        => 'required',
            'company' => 'required',
            'company_sector' => 'required',
            'job_position' => 'required',
            'job_experience_years' => 'required',
            'skills' => 'required',
            'specialties' => 'required',
            'industries' => 'required'
        ]);

        $input = $request->all();
        try {
            $this->mentorManager->editMentor($input, $id);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Mentor edited');
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
        $mentorId = $input['mentor_id'];
        if($mentorId == null || $mentorId == "") {
            session()->flash('flash_message_failure', 'Something went wrong. Please try again.');
            return back();
        }
        try {
            $this->mentorManager->deleteMentor($mentorId);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back();
        }
        session()->flash('flash_message_success', 'Mentor deleted');
        return back();
    }
}
