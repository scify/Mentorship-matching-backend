<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MailManager;
use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\User;
use App\Notifications\MentorStatusReactivation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HomeController extends Controller {
    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function dashBoard() {
        return view('home.dashboard', ['pageTitle' => "Dashboard"]);
    }


    public function testEmail(Request $request) {
        $input = $request->all();
        if (!isset($input['email'])) {
            throw new BadRequestHttpException("No email parameter");
        }
        $email = $input['email'];

        if ($email != "" && $email != null) {
            (new MailManager())->sendEmailToSpecificEmail(
                'emails.test',
                [],
                'Job Pairs | Test',
                $email
            );

            $userToTestMailable = User::where(['email' => 'paul@scify.org'])->first();

            if ($userToTestMailable)
                $userToTestMailable->notify(new MentorStatusReactivation(MentorProfile::first()));

            return "Email was sent";
        } else {
            throw new BadRequestHttpException("No email parameter");
        }
    }

    public function showServerInfoPage() {
        return view('common.server-info');
    }

}
