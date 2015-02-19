<?php namespace Pixel\Http\Controllers;

use Pixel\Contracts\User\UserContract;
use Pixel\Exceptions\User\InvalidActivationCodeException;
use Pixel\Exceptions\User\InvalidActivationTokenException;
use Pixel\Exceptions\User\UserNotActiveException;
use Pixel\Exceptions\User\UserNotFoundException;
use Pixel\Http\Requests\UserActivationRequest;
use Pixel\Http\Requests\UserLoginRequest;
use Pixel\Http\Requests\UserRegistrationRequest;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class UserAuthController
 * @package Pixel\Http\Controllers
 */
class UserAuthController extends Controller {

    /**
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new authentication controller instance
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;

        $this->middleware('guest', ['except' => ['logout', 'activate', 'doActivate']]);
    }

    /**
     * Show the registration form
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('users/auth/register');
    }

    /**
     * Handle a registration request for the application
     *
     * @param UserRegistrationRequest $request
     * @param UserContract            $userService
     *
     * @return \Illuminate\Http\Response
     */
    public function doRegister(UserRegistrationRequest $request, UserContract $userService)
    {
        $user = $userService->register( $request->only('name', 'email', 'password') );

        // If the user was explicitly activated, login and redirect home
        if ( $user->isActive() ) {
            $this->auth->login($user);
            return redirect()->route('home');
        }

        // Redirect the user to the activation form to complete registration
        return redirect()->route('users.auth.activate');
    }

    /**
     * Cancel a pending registration request
     *
     * @param UserActivationRequest $request
     * @param UserContract          $userService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function abortRegister(UserActivationRequest $request, UserContract $userService)
    {
        $errorResponse = 'The validation code provided is invalid';

        // Fetch the user we are activating
        try {
            if ($uid = $request->get('uid')) {
                $user = $userService->retrieveById($uid);
            } else {
                $user = $userService->retrieveByCredentials(['email' => $request->get('email')]);
            }
        } catch (UserNotFoundException $e) {
            return response()->redirectToRoute('users.auth.activate')->withErrors(['code' => $errorResponse]);
        }

        // Attempt to cancel a pending registration
        try {
            $userService->cancelRegistration($user, $request->get('code'));
        } catch (InvalidActivationCodeException $e) {
            return response()->redirectToRoute('users.auth.activate')->withErrors(['code' => $errorResponse]);
        }

        // Registration cancelled successfully, redirect back home
        return response()->redirectToRoute('home');
    }

    /**
     * Show the account activation form
     *
     * @return \Illuminate\Http\Response
     */
    public function activate()
    {
        return view('users/auth/activate');
    }

    /**
     * Process an account activation request
     *
     * @param UserActivationRequest $request
     * @param UserContract          $userService
     *
     * @return \Illuminate\Http\Response
     */
    public function doActivate(UserActivationRequest $request, UserContract $userService)
    {
        $errorResponse = 'The validation code provided is invalid';

        // Fetch the user we are activating
        try {
            if ($uid = $request->get('uid')) {
                $user = $userService->retrieveById($uid);
            } else {
                $user = $userService->retrieveByCredentials(['email' => $request->get('email')]);
            }
        } catch (UserNotFoundException $e) {
            return response()->redirectToRoute('users.auth.activate')->withErrors(['code' => $errorResponse]);
        }

        // Attempt to activate the users account
        try {
            $userService->activate($user, $request->get('code'));
        } catch (InvalidActivationCodeException $e) {
            return response()->redirectToRoute('users.auth.activate')->withErrors(['code' => $errorResponse]);
        } catch (InvalidActivationTokenException $e) {
            \Session::flash('message', 'Thank you, your account has been activated. You may now log in.');
            return response()->redirectToRoute('users.auth.login');
        }

        // The account was activated successfully and the user has a valid activation login token in their session
        $this->auth->login($user);
        return response()->redirectToRoute('home');
    }

    /**
     * Show the account login form
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('users/auth/login');
    }

    /**
     * Handle a login request to the application
     *
     * @param UserLoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function doLogin(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Attempt authentication. If the users account has not been activated, redirect to the activation page
        try {
            if ($this->auth->attempt($credentials, $request->has('remember')))
            {
                return redirect()->intended( route('home') );
            }
        } catch(UserNotActiveException $e) {
            return redirect()->route('users.auth.activate');
        }

        // If the authentication failed, redirect back to the login form
        return redirect( route('users.auth.login') )
            ->withInput( $request->only('email', 'remember') )
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * Log the user out of the application
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->auth->logout();

        return redirect('/');
    }

}