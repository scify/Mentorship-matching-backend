<?php
/**
 * Created by IntelliJ IDEA.
 * User: pisaris
 * Date: 2/2/2017
 * Time: 12:45 μμ
 */

namespace App\Interfaces;


use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

trait CustomAuthentication {

    use AuthenticatesUsers{
        attemptLogin as performLogin;
        username as getUsername;
    }

    public function attemptLogin(Request $request){
        $loginCredentials = $this->credentials($request);
        $loginCredentials['email'] = $loginCredentials['email_address'];
        unset($loginCredentials['email_address']);
        $loginCredentials['state_id'] = 1;
        return $this->guard()->attempt($loginCredentials, $request->has('remember'));
    }

    public function username()
    {
        return 'email_address';
    }
}