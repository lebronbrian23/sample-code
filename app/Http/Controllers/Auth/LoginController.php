<?php

namespace SampleProject\Http\Controllers\Auth;

use SampleProject\Http\Controllers\Controller;
use SampleProject\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/client';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->user = new User;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'mobile_no' => 'required|regex:/[0-9]{10}/|digits:12',
        ]);
        $user = User::where('mobile_no', $request->get('mobile_no'))->first();

        if($user) {
            Auth::login($user);
            return redirect('/');
        }
        else{
            return redirect()->back()->with('error', 'Your mobile phone number is not in our system..!!');
        }
    }
}
