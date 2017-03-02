<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\CompanyManager;
use App\BusinessLogicLayer\managers\UserManager;
use App\BusinessLogicLayer\managers\UserRoleManager;
use App\Http\OperationResponse;
use App\Models\eloquent\User;
use Illuminate\Http\Request;
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
        $pageTitle = 'All users';
        $loggedInUser = Auth::user();
        return view('users.list_all', [
            'pageTitle' => $pageTitle,
            'users' => $users, 'userRoles' => $userRoles, 'loggedInUser' => $loggedInUser]);
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
        return view('users.profile', ['user' => $user, 'loggedInUser' => $loggedInUser]);
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
        $userRoleIds = array();
        $formTitle = 'FILL IN THE FOLLOWING FORM IN ORDER TO REGISTER A NEW USER';
        $userRoles = $this->userRoleManager->getAllUserRoles();
        $companies = $companyManager->getAllUnassignedCompanies();
        return view('users.forms.create_edit', [
            'pageTitle' => 'Create new user',
            'user' => $user,
            'formTitle' => $formTitle, 'userRoles' => $userRoles,
            'userRoleIds' => $userRoleIds, 'companies' => $companies
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
            'email' => 'required|max:255|email',
            'user_roles' => 'required',
            'password'        => 'required|min:4|max:12',
            'passwordconfirm' => 'required|same:password'
        ]);

        $input = $request->all();

        try {
            $this->userManager->createUser($input);
        }  catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " .  $e->getMessage());
            return back()->withInput();
        }

        session()->flash('flash_message_success', 'User created');
        return $this->showAllUsers();

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
        $userRoleIds = $this->userRoleManager->getUserRoleIds($user);
        $formTitle = 'Edit user';
        $userRoles = $this->userRoleManager->getAllUserRoles();
        $companies = $companyManager->getCompaniesWithNoAccountManagerAssignedExceptAccountManager($user);

        if($user->company != null)
            $user['company_id'] = $user->company->id;
        else
            $user['company_id'] = null;

        return view('users.forms.create_edit', ['user' => $user,
            'formTitle' => $formTitle, 'userRoles' => $userRoles,
            'userRoleIds' => $userRoleIds, 'companies' => $companies
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
            'passwordconfirm' => 'same:password'
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

    public function getUsersByRole (Request $request) {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            $errorMessage = "Please select a role";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        }
        $input = $request->all();
        $roleId = (int) $input['role_id'];
        //dd($roleId);
        $users = $this->userRoleManager->getUsersByRoleId($roleId);
        //dd($users);
        if($users->count() == 0) {
            $errorMessage = "No users found";
            return json_encode(new OperationResponse(config('app.OPERATION_FAIL'), (String) view('common.ajax_error_message', compact('errorMessage'))));
        } else {
            $loggedInUser = Auth::user();
            return json_encode(new OperationResponse(config('app.OPERATION_SUCCESS'), (String) view('users.list', compact('users', 'loggedInUser'))));
        }
    }
}
