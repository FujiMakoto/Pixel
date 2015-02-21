<?php namespace Pixel\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Pixel\Contracts\User\UserContract;
use Pixel\Exceptions\User\InvalidActivationCodeException;
use Pixel\Exceptions\User\InvalidActivationTokenException;
use Pixel\Exceptions\User\UserNotActiveException;
use Pixel\Exceptions\User\UserNotFoundException;
use Pixel\Http\Requests\UserActivationRequest;
use Pixel\Http\Requests\UserLoginRequest;
use Pixel\Http\Requests\UserRecoveryRequest;
use Pixel\Http\Requests\UserRegistrationRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Pixel\Http\Requests\UserResetRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserAuthController
 * @package Pixel\Http\Controllers
 */
class UserAuthController extends Controller {

    use ResetsPasswords;

    /**
     * @var string
     */
    protected $redirectPath;

    /**
     * Create a new authentication controller instance
     */
    public function __construct()
    {
        $this->redirectPath = route('home');

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
            Auth::login($user);
            return redirect($this->redirectPath);
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
        return redirect($this->redirectPath);
    }

    /**
     * Handle an OAuth login / registration request
     *
     * @param String       $driver
     * @param Request      $request
     * @param UserContract $userService
     *
     * @return Response
     */
    public function oauth($driver, Request $request, UserContract $userService)
    {
        $oauthResponse = $userService->oauth($driver, $request->get('code'));

        if ($oauthResponse instanceof Response) return $oauthResponse;
        if ($oauthResponse instanceof Authenticatable) Auth::login($oauthResponse);

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
        Auth::login($user);
        return redirect($this->redirectPath);
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
            if (Auth::attempt($credentials, $request->has('remember')))
            {
                return redirect()->intended($this->redirectPath);
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
     * Display the form to request a password reset link
     *
     * @return Response
     */
    public function recover()
    {
        return view('users/auth/recover');
    }

    /**
     * Send a reset link to the given user
     *
     * @param UserRecoveryRequest $request
     *
     * @return Response
     */
    public function doRecover(UserRecoveryRequest $request)
    {
        $response = $this->passwords->sendResetLink($request->only('email'), function($message)
        {
            $message->subject($this->getEmailSubject());
        });

        switch ($response)
        {
            case PasswordBroker::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));

            case PasswordBroker::INVALID_USER:
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Display the password reset form for the given token
     *
     * @param  string  $token
     * @return Response
     */
    public function reset($token)
    {
        return view('users/auth/reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param UserResetRequest $request
     *
     * @return Response
     */
    public function doReset(UserResetRequest $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $this->passwords->reset($credentials, function($user, $password)
        {
            $user->password = bcrypt($password);

            $user->save();

            Auth::login($user);
        });

        switch ($response)
        {
            case PasswordBroker::PASSWORD_RESET:
                return redirect($this->redirectPath());

            default:
                return redirect()->back()
                                 ->withInput($request->only('email'))
                                 ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Log the user out of the application
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

}