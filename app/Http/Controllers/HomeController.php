<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MailManager;
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
            return "Email was sent";
        } else {
            throw new Exception("No email parameter");
        }

    }

}
