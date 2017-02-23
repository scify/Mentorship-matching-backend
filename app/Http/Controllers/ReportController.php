<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display all reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAllReports()
    {
        return view('reports.index', [
            'pageTitle' => 'Reports'
        ]);

//        $mentees = $this->menteeManager->getAllMentees();
//        $loggedInUser = Auth::user();
//        $page_title = 'All mentees';
//        return view('mentees.list_all', ['mentees' => $mentees, 'loggedInUser' => $loggedInUser, 'page_title' => $page_title]);
    }
}
