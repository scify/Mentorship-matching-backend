<?php

namespace App\Http\Controllers;

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
        $mentor = new MentorProfile();
        $mentorSpecialtiesIds = array();
        $mentorIndustriesIds = array();
        $formTitle = 'Create a new mentor';
        $specialties = $this->specialtyManager->getAllSpecialties();
        $industries = $this->industryManager->getAllIndustries();
        $residences = $this->residenceManager->getAllResidences();

        return view('mentors.forms.create_edit', ['mentor' => $mentor,
            'formTitle' => $formTitle, 'residences' => $residences,
            'specialties' => $specialties, 'industries' => $industries,
            'mentorSpecialtiesIds' => $mentorSpecialtiesIds,
            'mentorIndustriesIds' => $mentorIndustriesIds
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
}
