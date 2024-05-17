<?php

namespace App\Http\Controllers;

use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\User;
use App\Notifications\MentorStatusReactivation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HomeController extends Controller {
    /**
     * Show the application dashboard.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
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

            $userToTestMailable = User::where(['email' => $email])->first();

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
