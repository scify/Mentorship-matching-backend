<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\CompanyManager;
use App\BusinessLogicLayer\managers\EducationLevelManager;
use App\BusinessLogicLayer\managers\IndustryManager;
use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\MentorshipSessionStatusManager;
use App\BusinessLogicLayer\managers\MentorStatusManager;
use App\BusinessLogicLayer\managers\ReferenceManager;
use App\BusinessLogicLayer\managers\ResidenceManager;
use App\BusinessLogicLayer\managers\SpecialtyManager;
use App\BusinessLogicLayer\managers\UniversityManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\Http\OperationResponse;
use App\Models\eloquent\MentorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{

    private $mentorManager;
    private $specialtyManager;
    private $industryManager;
    private $residenceManager;
    private $educationLevelManager;
    private $universityManager;
    private $companyManager;
    private $referenceManager;
    private $mentorStatusManager;
    private $mentorshipSessionManager;

    public function __construct() {
        $this->specialtyManager = new SpecialtyManager();
        $this->industryManager = new IndustryManager();
        $this->mentorManager = new MentorManager();
        $this->residenceManager = new ResidenceManager();
        $this->educationLevelManager = new EducationLevelManager();
        $this->universityManager = new UniversityManager();
        $this->companyManager = new CompanyManager();
        $this->referenceManager = new ReferenceManager();
        $this->mentorStatusManager = new MentorStatusManager();
        $this->mentorshipSessionManager = new MentorshipSessionManager();
    }

    /**
     * Display all mentors.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllMentors() {
        $mentorViewModels = $this->mentorManager->getAllMentorViewModels();
        $loggedInUser = Auth::user();
        $specialties = $this->specialtyManager->getAllSpecialties();
        $companies = $this->companyManager->getAllCompanies();
        $statuses = $this->mentorStatusManager->getAllMentorStatuses();
        $residences = $this->residenceManager->getAllResidences();
        return view('mentors.list_all', [
            'pageTitle' => 'Mentors',
            'pageSubTitle' => 'view all',
            'mentorViewModels' => $mentorViewModels,
            'loggedInUser' => $loggedInUser,
            'specialties' => $specialties,
            'companies' => $companies,
            'statuses' => $statuses,
            'residences' => $residences
        ]);
    }

    public function showMentorsByCriteria(Request $request) {
        $input = $request->all();

        try {
            $mentorViewModels = $this->mentorManager->getMentorViewModelsByCriteria($input);
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }

        if($mentorViewModels->count() == 0) {
            $errorMessage = "No mentors found";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        } else {
            $loggedInUser = Auth::user();
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), (String) view('mentors.list', compact('mentorViewModels', 'loggedInUser'))));
        }
    }

    /**
     * Display a mentor profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile($id)
    {
        $menteeManager = new MenteeManager();
        $userManager = new UserManager();
        $mentorViewModel = $this->mentorManager->getMentorViewModel($this->mentorManager->getMentor($id));
        $menteeViewModels = $menteeManager->getAllMenteeViewModels();
        $universities = $this->universityManager->getAllUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $accountManagers = $userManager->getAccountManagersWithRemainingCapacity();
        $mentorshipSessionViewModels = $this->mentorshipSessionManager->getMentorshipSessionViewModelsForMentor($id);
        $loggedInUser = Auth::user();
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        return view('mentors.profile', ['mentorViewModel' => $mentorViewModel,
            'menteeViewModels' => $menteeViewModels, 'universities' => $universities,
            'educationLevels' => $educationLevels,
            'accountManagers' => $accountManagers,
            'loggedInUser' => $loggedInUser,
            'statuses' => $statuses,
            'mentorshipSessionViewModels' => $mentorshipSessionViewModels]);
    }

    /**
     * Show the form for creating a new mentor.
     *
     * @param Request $request object containing request parameters
     * @return \Illuminate\Http\Response
     */
    public function showCreateForm(Request $request)
    {
        $input = $request->all();
        if(isset($input['lang'])) {
            $language = $request['lang'];
            App::setLocale($language);
        }

        $pageTitle = 'Mentors';
        $pageSubTitle = 'create new';
        $publicForm = false;
        // when on public form ,we do not want to present header with page title and subtitle
        if(isset($input['public'])) {
            if($input['public'] == 1) {
                $pageTitle = null;
                $pageSubTitle = null;
                $publicForm = true;
            }
        }

        $mentor = new MentorProfile();
        $mentorSpecialtiesIds = array();
        $mentorIndustriesIds = array();
        $formTitle = trans('messages.mentor_registration');

        $specialties = $this->specialtyManager->getAllSpecialties();
        $industries = $this->industryManager->getAllIndustries();
        $residences = $this->residenceManager->getAllResidences();
        $references = $this->referenceManager->getAllReferences();
        $companies = $this->companyManager->getAllCompanies();
        $universities = $this->universityManager->getAllUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $mentorStatuses = $this->mentorStatusManager->getMentorStatusesForMentorCreation();

        return view('mentors.forms.create_edit', [
            'pageTitle' => $pageTitle,
            'pageSubTitle' => $pageSubTitle,
            'mentor' => $mentor,
            'formTitle' => $formTitle, 'residences' => $residences,
            'specialties' => $specialties, 'industries' => $industries,
            'mentorSpecialtiesIds' => $mentorSpecialtiesIds,
            'mentorIndustriesIds' => $mentorIndustriesIds, 'loggedInUser' => Auth::user(),
            'universities' => $universities, 'educationLevels' => $educationLevels,
            'companies' => $companies, 'references' => $references,
            'mentorStatuses' => $mentorStatuses, 'publicForm' => $publicForm
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
        $pageTitle = 'Edit mentor';

        $mentor = $this->mentorManager->getMentor($id);
        $specialties = $this->specialtyManager->getAllSpecialties();
        $industries = $this->industryManager->getAllIndustries();
        $residences = $this->residenceManager->getAllResidences();
        $references = $this->referenceManager->getAllReferences();
        $mentorSpecialtiesIds = $this->specialtyManager->getMentorSpecialtiesIds($mentor);
        $mentorIndustriesIds = $this->industryManager->getMentorIndustriesIds($mentor);
        $companies = $this->companyManager->getAllCompanies();
        $universities = $this->universityManager->getAllUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $mentorStatuses = $this->mentorStatusManager->getAllMentorStatuses();

        $formTitle = 'Edit mentor';
        return view('mentors.forms.create_edit', ['mentor' => $mentor,
            'formTitle' => $formTitle,
            'residences' => $residences, 'references' => $references,
            'specialties' => $specialties, 'industries' => $industries,
            'mentorSpecialtiesIds' => $mentorSpecialtiesIds,
            'mentorIndustriesIds' => $mentorIndustriesIds, 'loggedInUser' => Auth::user(),
            'universities' => $universities, 'educationLevels' => $educationLevels,
            'companies' => $companies,
            'mentorStatuses' => $mentorStatuses, 'pageTitle' => $pageTitle, 'publicForm' => false
        ]);
    }

    /**
     * Store a newly created mentor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'year_of_birth' => 'required|numeric|digits:4',
            'residence_id' => 'required',
            'reference_id' => 'required',
            'address'        => 'required',
            'education_level_id' => 'required',
            'university_id' => 'required',
            'university_name' => 'required_if:university_id,12',
            'company_id' => 'required',
            'company_sector' => 'required',
            'job_position' => 'required',
            'job_experience_years' => 'required',
            'skills' => 'required',
            'specialties' => 'required',
            'industries' => 'required',
            'status_id' => 'required'
        ]);

        $input = $request->all();

        try {
            $this->mentorManager->createMentor($input);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Mentor created');
        //if logged in user created the mentee, return to "all mentors" page
        if(Auth::user() != null)
            return redirect()->route('showAllMentors');
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
            'follow_up_date' => 'max:10|min:8',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'year_of_birth' => 'required|numeric|digits:4',
            'residence_id' => 'required',
            'address'        => 'required',
            'education_level_id' => 'required',
            'university_id' => 'required',
            'university_name' => 'required_if:university_id,12',
            'company_id' => 'required',
            'company_sector' => 'required',
            'job_position' => 'required',
            'job_experience_years' => 'required',
            'skills' => 'required',
            'specialties' => 'required',
            'industries' => 'required',
            'status_id' => 'required'
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

    /**
     * Change mentor availability status if you have the permissions to ONLY change the status
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function changeMentorAvailabilityStatus(Request $request) {
        $input = $request->all();

        try {
            $this->mentorManager->changeMentorAvailabilityStatus($input);
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }
        return redirect(route("showAllMentors"));
    }

    /**
     * When mentor wants to be available for new sessions after a completed session
     *
     * @param $id int The mentor's id
     * @param $email string The mentor's email
     * @return \Illuminate\View\View
     */
    public function makeMentorAvailableAgain($id, $email) {
        $viewTitle = "Availability Status Change";
        try {
            if($this->mentorManager->makeMentorAvailable($id, $email)) {
                return view('common.response-to-email')->with([
                    'message_success' => 'Your status has been successfully changed',
                    'title' => $viewTitle
                ]);
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => 'Mentor not found',
                    'title' => $viewTitle
                ]);
            }
        } catch(\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return view('common.response-to-email')->with([
                'message_failure' => $errorMessage,
                'title' => $viewTitle
            ]);
        }
    }
}
