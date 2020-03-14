<?php

namespace SampleProject\Http\Controllers\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SampleProject\Http\Controllers\Controller;
use SampleProject\Notifications\WelcomeNotification;
use SampleProject\WantedSasa;
use SampleProject\sms;
use SampleProject\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|regex:/[0-9]{10}/|digits:12',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \SampleProject\User
     */
    protected function create(array $data)
    {
        $checkUser = User::where('mobile_no','like', '%'.$data['mobile_no'].'%')->first();

        if ($checkUser) {
            return null;
        }

        $user = new User();
        $user->name = $data['name'];
        $user->user_no = WantedSasa::generateAccountN0();
        $user->mobile_no = $data['mobile_no'];
        $user->confirmation_code = WantedSasa::generatePIN(5);
        $user->country_code = substr($data['mobile_no'], 0, 3);
        $user->ip = request()->ip();
        $user->save();


        $user->makeUser('Client');
        $sms = new Sms();
        $sms->user_id = $user->id;
        $sms->no_of_sms = 5;
        $sms->ip = '127.0.0.1';
        $sms->save();

        $user->notify(new WelcomeNotification($user));

        return $user;

    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        if(!$user) return redirect()->back()->with('error','User with that phone number already exists ! ');

        event(new Registered($user));

        $this->guard()->login($user);

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }
}
