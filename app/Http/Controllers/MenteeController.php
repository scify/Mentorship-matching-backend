<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\CompanyManager;
use App\BusinessLogicLayer\managers\EducationLevelManager;
use App\BusinessLogicLayer\managers\MenteeManager;
use App\BusinessLogicLayer\managers\MenteeStatusManager;
use App\BusinessLogicLayer\managers\MentorManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\MentorStatusManager;
use App\BusinessLogicLayer\managers\ReferenceManager;
use App\BusinessLogicLayer\managers\ResidenceManager;
use App\BusinessLogicLayer\managers\SpecialtyManager;
use App\BusinessLogicLayer\managers\UniversityManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\Http\OperationResponse;
use App\Models\eloquent\MenteeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class MenteeController extends Controller
{
    private $menteeManager;
    private $specialtyManager;
    private $residenceManager;
    private $referenceManager;
    private $universityManager;
    private $educationLevelManager;
    private $menteeStatusManager;
    private $mentorshipSessionManager;

    public function __construct() {
        $this->specialtyManager = new SpecialtyManager();
        $this->menteeManager = new MenteeManager();
        $this->residenceManager = new ResidenceManager();
        $this->referenceManager = new ReferenceManager();
        $this->universityManager = new UniversityManager();
        $this->educationLevelManager = new EducationLevelManager();
        $this->menteeStatusManager = new MenteeStatusManager();
        $this->mentorshipSessionManager = new MentorshipSessionManager();
    }

    /**
     * Display all mentees.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllMentees()
    {
        $menteeViewModels = $this->menteeManager->getAllMenteeViewModels();
        $universities = $this->universityManager->getAllUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $statuses = $this->menteeStatusManager->getAllMenteeStatuses();
        $loggedInUser = Auth::user();
        $page_title = 'All mentees';
        return view('mentees.list_all', [
            'pageTitle' => $page_title,
            'menteeViewModels' => $menteeViewModels, 'universities' => $universities,
            'educationLevels' => $educationLevels, 'statuses' => $statuses,
            'loggedInUser' => $loggedInUser]);
    }

    /**
     * Show the form for creating a new mentee.
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

        $pageTitle = 'Mentees';
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

        $mentee = new MenteeProfile();
        $formTitle = trans('messages.mentee_registration');
        $specialties = $this->specialtyManager->getAllSpecialties();
        $residences = $this->residenceManager->getAllResidences();
        $references = $this->referenceManager->getAllReferences();
        $universities = $this->universityManager->getAllUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $menteeStatuses = $this->menteeStatusManager->getAllMenteeStatuses();

        return view('mentees.forms.create_edit', [
            'pageTitle' => $pageTitle,
            'pageSubTitle' => $pageSubTitle,
            'mentee' => $mentee, 'references' => $references,
            'formTitle' => $formTitle, 'residences' => $residences,
            'specialties' => $specialties, 'universities' => $universities,
            'educationLevels' => $educationLevels, 'menteeStatuses' => $menteeStatuses,
            'loggedInUser' => Auth::user(),'publicForm' => $publicForm
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
        $mentee = $this->menteeManager->getMentee($id);

        $specialties = $this->specialtyManager->getAllSpecialties();
        $residences = $this->residenceManager->getAllResidences();
        $references = $this->referenceManager->getAllReferences();
        $universities = $this->universityManager->getAllUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $menteeStatuses = $this->menteeStatusManager->getAllMenteeStatuses();

        $formTitle = 'Edit mentee';
        return view('mentees.forms.create_edit', [
            'pageTitle' => 'Edit mentee',
            'mentee' => $mentee, 'references' => $references,
            'formTitle' => $formTitle, 'residences' => $residences,
            'specialties' => $specialties, 'universities' => $universities,
            'educationLevels' => $educationLevels, 'menteeStatuses' => $menteeStatuses,
            'loggedInUser' => Auth::user(), 'publicForm' => false
        ]);
    }

    /**
     * Store a newly created mentee in storage.
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
            'university_department_name' => 'required',
            'university_graduation_year' => 'required',
            'specialty_experience' => 'required',
            'specialty_id' => 'required',
            'expectations' => 'required',
            'career_goals' => 'required',
            'cv_file' => 'file',
            'public_form' => 'required|mimes:pdf|max:10000'
        ]);

        $input = $request->all();

        try {
            $this->menteeManager->createMentee($input,
                ($request->hasFile('cv_file') && $request->file('cv_file')->isValid()) ? true : false);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Mentee created');
        //if logged in user created the mentee, return to "all mentors" page
        if(Auth::user() != null)
            return redirect()->route('showAllMentees');
        return back();

    }

    /**
     * Display a mentee profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile($id)
    {
        $specialties = $this->specialtyManager->getAllSpecialties();
        $companies = (new CompanyManager())->getAllCompanies();
        $statuses = (new MentorStatusManager())->getAllMentorStatuses();
        $residences = $this->residenceManager->getAllResidences();
        $accountManagers = (new UserManager())->getAccountManagersWithRemainingCapacity();
        $menteeViewModel = $this->menteeManager->getMenteeViewModel($this->menteeManager->getMentee($id));
        $mentorManager = new MentorManager();
        $availableMentorViewModels = $mentorManager->getAvailableMentorViewModels();
        $currentSessionViewModel = $this->mentorshipSessionManager->getCurrentMentorshipSessionViewModelForMentee($id);
        $mentorshipSessionViewModels = $this->mentorshipSessionManager->getMentorshipSessionViewModelsForMentee($id);
        $loggedInUser = Auth::user();
        return view('mentees.profile', ['menteeViewModel' => $menteeViewModel, 'loggedInUser' => $loggedInUser,
            'specialties' => $specialties, 'companies' => $companies, 'statuses' => $statuses,
            'residences' => $residences, 'accountManagers' => $accountManagers,
            'availableMentorViewModels' => $availableMentorViewModels,
            'mentorshipSessionViewModels' => $mentorshipSessionViewModels,
            'currentSessionViewModel' => $currentSessionViewModel
        ]);
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
            'university_department_name' => 'required',
            'university_graduation_year' => 'required',
            'specialty_experience' => 'required',
            'specialty_id' => 'required',
            'expectations' => 'required',
            'career_goals' => 'required',
            'cv_file' => 'file|mimes:pdf|max:10000',
        ]);

        $input = $request->all();
        try {
            $this->menteeManager->editMentee($input, $id,
                ($request->hasFile('cv_file') && $request->file('cv_file')->isValid()) ? true : false);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'Mentee edited');
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
        $menteeId = $input['mentee_id'];
        if($menteeId == null || $menteeId == "") {
            session()->flash('flash_message_failure', 'Something went wrong. Please try again.');
            return back();
        }
        try {
            $this->menteeManager->deleteMentee($menteeId);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back();
        }
        session()->flash('flash_message_success', 'Mentee deleted');
        return back();
    }

    public function showMenteesByCriteria(Request $request) {
        $input = $request->all();
        try {
            $menteeViewModels = $this->menteeManager->getMenteeViewModelsByCriteria($input);
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }

        if($menteeViewModels->count() == 0) {
            $errorMessage = "No mentees found";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        } else {
            $loggedInUser = Auth::user();
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), (String) view('mentees.list', compact('menteeViewModels', 'loggedInUser'))));
        }
    }

    /**
     * Change mentee availability status if you have the permissions to ONLY change the status
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function changeMenteeAvailabilityStatus(Request $request) {
        $input = $request->all();

        try {
            $this->menteeManager->changeMenteeAvailabilityStatus($input);
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }
        return redirect(route("showAllMentees"));
    }
}
