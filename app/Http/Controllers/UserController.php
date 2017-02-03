<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\UserManager;
use App\BusinessLogicLayer\managers\UserRoleManager;
use App\Models\eloquent\User;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateForm()
    {
        $user = new User();
        $userRoleIds = array();
        $formTitle = 'Create a new user';
        $userRoles = $this->userRoleManager->getAllUserRoles();
        return view('users.forms.create_edit', ['user' => $user, 'formTitle' => $formTitle, 'userRoles' => $userRoles, 'userRoleIds' => $userRoleIds]);
    }

    /**
     * Store a newly created resource in storage.
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

        session()->flash('flash_message_success', 'Game card edited');
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEditForm($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
    }
}
