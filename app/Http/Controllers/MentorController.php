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

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

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
        $mentorViewModels = $this->mentorManager->paginateMentors($this->mentorManager->getAllMentorViewModels())->setPath('#');
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
            $mentorViewModelsData = $this->mentorManager->getMentorViewModelsByCriteria($input);
            $mentorViewModels = $this->mentorManager->paginateMentors($mentorViewModelsData)->setPath('#');
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }

        if($mentorViewModels && $mentorViewModels->count() == 0) {
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
        $availableMenteeViewModels = $menteeManager->paginateMentees($menteeManager->getAvailableMenteeViewModels())->setPath('#');
        $universities = $this->universityManager->getAllUniversitiesIncludingOtherUniversities();
        $educationLevels = $this->educationLevelManager->getAllEducationLevels();
        $accountManagers = $userManager->getAccountManagersWithRemainingCapacity();
        $currentSessionViewModel = $this->mentorshipSessionManager
            ->paginateMentorshipSessions($this->mentorshipSessionManager->getCurrentMentorshipSessionViewModelForMentor($id))->setPath("#");
        $mentorshipSessionViewModels = $this->mentorshipSessionManager
            ->paginateMentorshipSessions($this->mentorshipSessionManager
            ->getMentorshipSessionViewModelsForMentor($id), 100)->setPath("#");
        $specialties = $this->specialtyManager->getAllSpecialties();
        $loggedInUser = Auth::user();
        $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
        $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        $displayOnlyAvailableMentees = true;
        return view('mentors.profile', ['mentorViewModel' => $mentorViewModel,
            'availableMenteeViewModels' => $availableMenteeViewModels, 'universities' => $universities,
            'educationLevels' => $educationLevels,
            'accountManagers' => $accountManagers,
            'loggedInUser' => $loggedInUser,
            'statuses' => $statuses,
            'specialties' => $specialties,
            'mentorshipSessionViewModels' => $mentorshipSessionViewModels,
            'currentSessionViewModel' => $currentSessionViewModel,
            'displayOnlyAvailableMentees' => $displayOnlyAvailableMentees
        ]);
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
        $language = "en";
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

        $loggedInUser = Auth::user();
        $enableSpecialtiesInsertion = !empty($loggedInUser);

        // when it is not a public form, get all specialties, else get only public specialties
        if (!empty($loggedInUser)) {
            $specialties = $this->specialtyManager->getAllSpecialties();
        } else {
            $specialties = $this->specialtyManager->getPublicSpecialties();
        }

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
            'mentorIndustriesIds' => $mentorIndustriesIds, 'loggedInUser' => $loggedInUser,
            'universities' => $universities, 'educationLevels' => $educationLevels,
            'companies' => $companies, 'references' => $references,
            'mentorStatuses' => $mentorStatuses, 'publicForm' => $publicForm, 'language' => $language,
            'enableSpecialtiesInsertion' => $enableSpecialtiesInsertion
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
        $language = "en";
        $mentor = $this->mentorManager->getMentor($id);

        $loggedInUser = Auth::user();
        $enableSpecialtiesInsertion = !empty($loggedInUser);

        // when it is not a public form, get all specialties, else get only public specialties
        if (!empty($loggedInUser)) {
            $specialties = $this->specialtyManager->getAllSpecialties();
        } else {
            $specialties = $this->specialtyManager->getPublicSpecialties();
        }

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
            'mentorIndustriesIds' => $mentorIndustriesIds, 'loggedInUser' => $loggedInUser,
            'universities' => $universities, 'educationLevels' => $educationLevels,
            'companies' => $companies,
            'mentorStatuses' => $mentorStatuses, 'pageTitle' => $pageTitle, 'publicForm' => false,
            'language' => $language, 'enableSpecialtiesInsertion' => $enableSpecialtiesInsertion
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

        $input = $request->all();

        if(isset($input['lang'])) {
            $language = $request['lang'];
            App::setLocale($language);
        }
        $publicForm = $input['public_form'];
        if($publicForm == "true") {
            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|max:255|email|unique:mentor_profile',
                'year_of_birth' => 'required|numeric|digits:4',
                'residence_id' => 'required',
                'residence_name' => 'required_if:residence_id,4',
                'reference_id' => 'required',
                'address' => 'required',
                'education_level_id' => 'required',
                'university_id' => 'required',
                'university_name' => 'required_if:university_id,12',
                'company_id' => 'required',
                'company_sector' => 'required',
                'job_position' => 'required',
                'job_experience_years' => 'required|numeric|min:5',
                'skills' => 'required',
                'specialties' => 'required',
                'industries' => 'required',
                'cv_file' => 'file|mimes:doc,pdf,docx|max:10000',
                'public_form' => 'required',
                'linkedin_url' => 'url'
            ], $this->messages());
        } else {
            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'year_of_birth' => 'required|numeric|digits:4',
                'email' => 'required|max:255|email|unique:mentor_profile',
                'cv_file' => 'file|mimes:doc,pdf,docx|max:10000',
                'public_form' => 'required',
                'linkedin_url' => 'url'
            ], $this->messages());
        }

        try {
            // checks if cv is valid and pass a parameter that shows if a cv file exists
            $this->mentorManager->createMentor($input,
                ($request->hasFile('cv_file') && $request->file('cv_file')->isValid()) ? true : false);
        }  catch (\Exception $e) {
            Log::info('Error on mentor creation: ' . $e->getCode() . "  " .  $e->getMessage());
            session()->flash('flash_message_failure', 'An error occurred. Please try again.');
            return back()->withInput();
        }


        //if logged in user created the mentor, return to "all mentors" page
        if(Auth::user() != null && $input['public_form'] !== "true") {
            session()->flash('flash_message_success', 'Mentor ' . $input['first_name'] . ' ' . $input['last_name'] . ' created');
            return redirect()->route('showAllMentors');
        } else {
            session()->flash('flash_message_success', trans("messages.mentor_created_public"));
            return back();
        }


    }

    public function messages()
    {
        return [
            'first_name.required' => trans('messages.first_name.required'),
            'last_name.required' => trans('messages.last_name.required'),
            'residence_id.required' => trans('messages.residence_id.required'),
            'residence_name.required_if' => trans('messages.residence_name.required'),
            'email.required' => trans('messages.email.required'),
            'email.unique' => trans('messages.email.unique'),
            'year_of_birth.required' => trans('messages.year_of_birth.required'),
            'reference_id.required' => trans('messages.reference_id.required'),
            'address.required' => trans('messages.address.required'),
            'education_level_id.required' => trans('messages.education_level_id.required'),
            'university_id.required' => trans('messages.university_id.required'),
            'university_name.required_if' => trans('messages.university_name.required'),
            'company_id.required' => trans('messages.company_id.required'),
            'company_sector.required' => trans('messages.company_sector.required'),
            'job_position.required' => trans('messages.job_position.required'),
            'job_experience_years.required' => trans('messages.job_experience_years.required'),
            'skills.required' => trans('messages.skills.required'),
            'specialties.required' => trans('messages.specialties.required'),
            'industries.required' => trans('messages.industries.required'),
            'status_id.required' => trans('messages.status_id.required'),
            'job_experience_years.min' => trans('messages.job_experience_years.min'),
            'cv_file.max' => trans('messages.cv_file.max'),
            'cv_file.mimes' => trans('messages.cv_file.mimes')
        ];
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
        $input = $request->all();
        if(isset($input['lang'])) {
            $language = $request['lang'];
            App::setLocale($language);
        }

//        $this->validate($request, [
//            'follow_up_date' => 'max:10|min:8',
//            'first_name' => 'required|max:255',
//            'last_name' => 'required|max:255',
//            'email' => 'required|max:255|email',
//            'year_of_birth' => 'required|numeric|digits:4',
//            'residence_id' => 'required',
//            'residence_name' => 'required_if:residence_id,4',
//            'address'        => 'required',
//            'education_level_id' => 'required',
//            'university_id' => 'required',
//            'university_name' => 'required_if:university_id,12',
//            'company_id' => 'required',
//            'company_sector' => 'required',
//            'job_position' => 'required',
//            'job_experience_years' => 'required',
//            'skills' => 'required',
//            'specialties' => 'required',
//            'industries' => 'required',
//            'status_id' => 'required',
//            'cv_file' => 'file|mimes:doc,pdf,docx|max:10000',
//        ], $this->messages());
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'year_of_birth' => 'required|numeric|digits:4',
            'email' => 'required|max:255|email|unique:mentor_profile,email,' . $id,
            'cv_file' => 'file|mimes:doc,pdf,docx|max:10000',
            'public_form' => 'required'
        ], $this->messages());

        try {
            $this->mentorManager->editMentor($input, $id,
                ($request->hasFile('cv_file') && $request->file('cv_file')->isValid()) ? true : false);
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
    public function makeMentorAvailableAgain($id, $email,Request $request)
    {
        $lang = $request->has('lang') ? $request->get('lang') : 'en';
        App::setLocale($lang);
        $viewTitle = Lang::get('messages.availability_change_title');
        try {
            $resultStatusCode = $this->mentorManager->makeMentorAvailable($id, $email);
            if($resultStatusCode === "SUCCESS") {
                return view('common.response-to-email')->with([
                    'message_success' => Lang::get('messages.status_change_success'),
                    'title' => $viewTitle
                ]);
            } else if($resultStatusCode === "NOT_FOUND") {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.mentor_not_found'),
                    'title' => $viewTitle
                ]);
            } else {
                return view('common.response-to-email')->with([
                    'message_failure' => Lang::get('messages.no_permissions'),
                    'title' => $viewTitle
                ]);
            }
        } catch(\Exception $e) {
            Log::info('Error on making mentor available after completion of session: ' . $e->getCode() . "  " .  $e->getMessage());
            return view('common.response-to-email')->with([
                'message_failure' => Lang::get('messages.error_occurred'),
                'title' => $viewTitle
            ]);
        }
    }
}
