<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MentorshipSessionController extends Controller
{
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
    public function matchMentorWithMentee(Request $request)
    {
        $input = $request->all();
        dd($input);
    }

    /**
     * Request contains the mentor and mentee id
     * as well as the account manager id
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function matchMeenteeWithMentor(Request $request)
    {
        $input = $request->all();
        dd($input);
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
