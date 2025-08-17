<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Override the authenticated method to check email verification.
     */
   protected function authenticated(Request $request, $user)
{
    if (!$user->hasVerifiedEmail()) {
        // Log the user out immediately
        //


        // Send verification email
        $user->sendEmailVerificationNotification();

        // Redirect back to login with message
        return redirect('/login')->with('message', 'Please verify your email address before logging in.');
    }

    // If verified, redirect to intended or default page
    return redirect()->intended('/home'); // Or any route you want
}

}
