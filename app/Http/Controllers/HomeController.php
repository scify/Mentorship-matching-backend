<?php

namespace App\Http\Controllers;

use App\Models\eloquent\MentorProfile;
use App\Models\eloquent\User;
use App\Notifications\MentorStatusReactivation;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HomeController extends Controller {

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

    /**
     * @throws Exception
     */
    public function testException() {
        throw new Exception("This is a test exception!!");
    }

}
