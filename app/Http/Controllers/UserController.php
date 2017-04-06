<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\CompanyManager;
use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use App\BusinessLogicLayer\managers\MentorshipSessionStatusManager;
use App\BusinessLogicLayer\managers\UserIconManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\BusinessLogicLayer\managers\UserRoleManager;
use App\BusinessLogicLayer\managers\MailManager;
use App\Http\OperationResponse;
use App\Models\eloquent\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userRoleManager;
    private $userManager;

    public function __construct() {
        $this->userRoleManager = new UserRoleManager();
        $this->userManager = new UserManager();
    }

    /**
     * Display all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllUsers()
    {
        $users = $this->userManager->getAllUsers();
        $userRoles = $this->userRoleManager->getAllUserRoles();
        $accountManagersActiveSessions = $this->userManager->getCountOfActiveSessionsForAllAccountManagers();
        $loggedInUser = Auth::user();
        return view('users.list_all', [
            'pageTitle' => 'System Users',
            'pageSubTitle' => 'view all',
            'users' => $users, 'userRoles' => $userRoles, 'accountManagersActiveSessions' => $accountManagersActiveSessions,
            'loggedInUser' => $loggedInUser]);
    }

    /**
     * Display a user profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile($id)
    {
        $user = $this->userManager->getUser($id);
        $loggedInUser = Auth::user();
        $mentorshipSessionViewModels = new Collection();
        $accountManagers = new Collection();
        $statuses = new Collection();
        if($loggedInUser->isAdmin() && $user->isAccountManager()) {
            $mentorshipSessionManager = new MentorshipSessionManager();
            $mentorshipSessionStatusManager = new MentorshipSessionStatusManager();
            $mentorshipSessionViewModels = $mentorshipSessionManager->getMentorshipSessionViewModelsForAccountManager($user->id);
            $accountManagers = $this->userManager->getAccountManagersWithRemainingCapacity();
            $statuses = $mentorshipSessionStatusManager->getAllMentorshipSessionStatuses();
        }
        return view('users.profile', [
            'user' => $user, 'loggedInUser' => $loggedInUser,
            'mentorshipSessionViewModels' => $mentorshipSessionViewModels,
            'accountManagers' => $accountManagers, 'statuses' => $statuses]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateForm()
    {
        $companyManager = new CompanyManager();
        $user = new User();
        $userIconsManager = new UserIconManager();
        $userRoleIds = array();
        $formTitle = 'FILL IN THE FOLLOWING FORM IN ORDER TO REGISTER A NEW USER';
        $userRoles = $this->userRoleManager->getAllUserRoles();
        $companies = $companyManager->getAllUnassignedCompanies();
        $userIcons = $userIconsManager->getAllUserIcons();
        return view('users.forms.create_edit', [
            'pageTitle' => 'System Users',
            'pageSubTitle' => 'create new user',
            'user' => $user,
            'formTitle' => $formTitle, 'userRoles' => $userRoles,
            'userRoleIds' => $userRoleIds, 'companies' => $companies,
            'userIcons' => $userIcons
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users',
            'user_roles' => 'required',
            'password'        => 'required|min:4|max:12',
            'passwordconfirm' => 'required|same:password',
            'capacity' =>'numeric|min:1|max:10'
        ]);

        $input = $request->all();

        try {
            $this->userManager->createUser($input);
            // send email with login credentials
            (new MailManager())->sendEmailToSpecificEmail('emails.register', 
                array('email' => $input['email'], 'password' => $input['password']), 
                'Your account has been created',
                $input['email']
            );
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'User ' . $input["email"] . ' has been created. 
            An email has been sent with account information'
        );
        return redirect('/users/all');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
        $user = $this->userManager->getUser($id);
        $userIconsManager = new UserIconManager();
        $userRoleIds = $this->userRoleManager->getUserRoleIds($user);
        $formTitle = 'EDIT USER';
        $userRoles = $this->userRoleManager->getAllUserRoles();
        $companies = $companyManager->getCompaniesWithNoAccountManagerAssignedExceptAccountManager($user);
        $userIcons = $userIconsManager->getAllUserIcons();

        if($user->company != null)
            $user['company_id'] = $user->company->id;
        else
            $user['company_id'] = null;

        return view('users.forms.create_edit', ['user' => $user,
            'pageTitle' => 'System Users',
            'pageSubTitle' => 'edit user',
            'formTitle' => $formTitle, 'userRoles' => $userRoles,
            'userRoleIds' => $userRoleIds, 'companies' => $companies,
            'userIcons' => $userIcons
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'user_roles' => 'required',
            'password'        => 'min:4|max:12',
            'passwordconfirm' => 'same:password',
            'capacity' =>'numeric|min:1|max:10'
        ]);
        $input = $request->all();
        try {
            $this->userManager->editUser($input, $id);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'User edited');
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
        $userId = $input['user_id'];
        if($userId == null || $userId == "") {
            session()->flash('flash_message_failure', 'Something went wrong. Please try again.');
            return back();
        }
        try {
            $this->userManager->deleteUser($userId);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back();
        }
        session()->flash('flash_message_success', 'User deleted');
        return back();
    }

    /**
     * Activates a given user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
    {
        $input = $request->all();
        $userId = $input['user_id'];
        if($userId == null || $userId == "") {
            session()->flash('flash_message_failure', 'Something went wrong. Please try again.');
            return back();
        }
        try {
            $this->userManager->activateUser($userId);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back();
        }
        session()->flash('flash_message_success', 'User activated');
        return back();
    }

    /**
     * Deactivates a given user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Request $request)
    {
        $input = $request->all();
        $userId = $input['user_id'];
        if($userId == null || $userId == "") {
            session()->flash('flash_message_failure', 'Something went wrong. Please try again.');
            return back();
        }
        try {
            $this->userManager->deactivateUser($userId);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back();
        }
        session()->flash('flash_message_success', 'User deactivated');
        return back();
    }

    public function getUsersByCriteria (Request $request) {
        $input = $request->all();

        try {
            $users = $this->userManager->getUsersByCriteria($input);
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }

        if($users->count() == 0) {
            $errorMessage = "No users found";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        } else {
            $loggedInUser = Auth::user();
            $accountManagersActiveSessions = $this->userManager->getCountOfActiveSessionsForAllAccountManagers();
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), (String) view('users.list', compact('users', 'accountManagersActiveSessions', 'loggedInUser'))));
        }
    }

    public function editUserCapacity(Request $request) {
        $input = $request->all();

        try {
            $this->userManager->editUserCapacity($input);
        }  catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getCode() . "  " .  $e->getMessage();
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }

        return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), "Success"));

    }
}
