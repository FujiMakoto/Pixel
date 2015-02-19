<?php namespace Pixel\Services\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Mail\Mailer;
use Pixel\Contracts\User\UserContract;
use Pixel\Contracts\User\RepositoryContract;
use Pixel\Exceptions\User\InvalidActivationCodeException;
use Pixel\Exceptions\User\InvalidActivationTokenException;
use Pixel\Exceptions\User\LoginRequiredException;
use Pixel\Exceptions\User\PasswordRequiredException;
use Pixel\Exceptions\User\PasswordRequiredExceptions;
use Pixel\Exceptions\User\UserNotActiveException;

class UserService implements UserContract {

    /**
     * The hasher implementation.
     *
     * @var HasherContract
     */
    protected $hasher;

    /**
     * @var RepositoryContract
     */
    protected $userRepo;

    /**
     * Constructor
     *
     * @param RepositoryContract $userRepo
     * @param HasherContract     $hasher
     */
    public function __construct(RepositoryContract $userRepo, HasherContract $hasher)
    {
        $this->userRepo = $userRepo;
        $this->hasher   = $hasher;
    }

    /**
     * Process a registration
     *
     * @param array $attributes
     *
     * @return $this
     * @throws LoginRequiredException
     * @throws PasswordRequiredException
     */
    public function register(array $attributes)
    {
        // Activation status and code
        $attributes['active'] = empty($attributes['active'])
            ? 0
            : $attributes['active'];
        $attributes['activation_code'] = $attributes['active']
            ? null
            : str_random(40);
        $attributes['activation_token'] = $attributes['active']
            ? null
            : str_random(100);

        // Email checking
        if ( empty($attributes['email']) )
            throw new LoginRequiredException('The email login field is required on registration');

        // Password hashing and checking
        if ( empty($attributes['password']) )
            throw new PasswordRequiredException('The password field is required on registration');

        $attributes['password'] = $this->hasher->make($attributes['password']);

        // Add activation information to the users session
        if ( ! $attributes['active']) {
            \Session::put('activation_token', $attributes['activation_token']);
            \Session::put('activation_email', $attributes['email']);
        }

        // Create the user and send an activation e-mail
        $user = $this->userRepo->create($attributes);
        $this->sendActivationEmail($user);

        return $user;
    }

    /**
     * Delete a pending registration
     *
     * @param Authenticatable $user
     * @param string|bool     $code
     *
     * @throws InvalidActivationCodeException
     */
    public function cancelRegistration(Authenticatable $user, $code = false)
    {
        // Validate our activation code
        if ( ($code !== false) && ( ! $user->validateActivationCode($code)) )
            throw new InvalidActivationCodeException('Invalid activation code supplied when cancelling registration');

        // Clear our session attributes (if we have any)
        \Session::remove('activation_token');
        \Session::remove('activation_email');

        $user->destroy();
    }

    /**
     * Activate a users account
     *
     * @param Authenticatable $user
     * @param string|bool     $code
     *
     * @throws InvalidActivationCodeException
     * @throws InvalidActivationTokenException
     */
    public function activate(Authenticatable $user, $code = false)
    {
        // Validate our activation code
        if ( ($code !== false) && ( ! $user->validateActivationCode($code)) )
            throw new InvalidActivationCodeException('Invalid activation code supplied when activating');

        // Activate the user
        $userToken = $user->getActivationToken();
        $this->userRepo->activate();

        // Clear our session attributes
        $sessionToken = \Session::pull('activation_token');
        \Session::remove('activation_email');

        // Do we have a valid activation token?
        if ( empty($sessionToken) || $userToken != $sessionToken )
            throw new InvalidActivationTokenException('No valid activation token was found in the users session');
    }

    /**
     * Queues an activation e-mail for the specified user
     *
     * @param Authenticatable $user
     */
    public function sendActivationEmail(Authenticatable $user)
    {
        \Mail::send('emails/activation', ['user' => $user], function($message) use ($user)
        {
            $message->to($user->email, $user->name)->subject(config('app.name').' Account Activation');
        });
    }

    /**
     * Retrieve a user by their unique identifier
     *
     * @param int $id
     *
     * @return RepositoryContract|null
     */
    public function retrieveById($id)
    {
        return $this->userRepo->getById($id);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token
     *
     * @param int    $id
     * @param string $token
     *
     * @return RepositoryContract|null
     */
    public function retrieveByToken($id, $token)
    {
        return $this->userRepo->getByToken($id, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage
     *
     * @param Authenticatable $user
     * @param string          $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
    }

    /**
     * Retrieve a user by the given credentials
     *
     * @param array $credentials
     *
     * @return RepositoryContract|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->userRepo->getByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials
     *
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
     * @throws UserNotActiveException
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Check our login credentials
        $validLogin = $this->hasher->check($credentials['password'], $user->getAuthPassword());
        if ( ! $validLogin) return false;

        // If the user is not active, throw an exception
        if ( ! $user->isActive()) {
            // Initialize the activation session so that the user can be automatically logged in after activation
            $user->setActivationToken( str_random(100) );
            \Session::put('activation_token', $user->getActivationToken());
            \Session::put('activation_email', $user->email);

            throw new UserNotActiveException('The account '.$user->email.' has not been activated');
        }

        return $validLogin;
    }


}