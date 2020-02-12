<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MailManager;
use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\User;
use App\Notifications\MenteeRegistered;
use App\Notifications\MentorStatusReactivation;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashBoard()
    {
        return view('home.dashboard', ['pageTitle' => "Dashboard"]);
    }


    public function testEmail(Request $request) {
        $input = $request->all();
        if(!isset($input['email'])) {
            throw new Exception("No email parameter");
        }
        $email = $input['email'];

        if($email != "" && $email != null) {
            (new MailManager())->sendEmailToSpecificEmail(
                'emails.test',
                [],
                'Job Pairs | Test',
                $email
            );

            $userToTestMailable = User::where(['email' => 'paul@scify.org'])->first();

            if($userToTestMailable)
                $userToTestMailable->notify(new MentorStatusReactivation(MentorProfile::first()));

            return "Email was sent";
        } else {
            throw new Exception("No email parameter");
        }




    }

    public function showServerInfoPage() {
        return view('common.server-info');
    }

}
