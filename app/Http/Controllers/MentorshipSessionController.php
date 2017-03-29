<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MentorshipSessionManager;
use Illuminate\Http\Request;

class MentorshipSessionController extends Controller
{
    private $mentorshipSessionManager;

    public function __construct() {
        $this->mentorshipSessionManager = new MentorshipSessionManager();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Request contains the mentor and mentee id
     * as well as the account manager id
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'mentor_profile_id' => 'required|numeric',
            'mentee_profile_id' => 'required|numeric',
            'account_manager_id' => 'required|numeric'
        ]);
        $input = $request->all();
        try {
            $this->mentorshipSessionManager->createMentorshipSession($input);
        } catch (\Exception $e) {
            session()->flash('flash_message_failure', 'Error: ' . $e->getCode() . "  " . $e->getMessage());
        }
        session()->flash('flash_message_success', 'Mentorship session created');
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
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
