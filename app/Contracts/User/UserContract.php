<?php namespace Pixel\Contracts\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Pixel\Contracts\User\RepositoryContract;

interface UserContract extends UserProvider {

    /**
     * Process a registration
     *
     * @param array $attributes
     *
     * @return RepositoryContract
     * @throws LoginRequiredException
     * @throws PasswordRequiredException
     */
    public function register(array $attributes);

    /**
     * Delete a pending registration
     *
     * @param Authenticatable $user
     * @param string|bool     $code
     *
     * @throws InvalidActivationCodeException
     */
    public function cancelRegistration(Authenticatable $user, $code = false);

    /**
     * Activate a users account
     *
     * @param Authenticatable $user
     * @param string|bool     $code
     *
     * @throws InvalidActivationCodeException
     * @throws InvalidActivationTokenException
     */
    public function activate(Authenticatable $user, $code = false);

    /**
     * Queues an activation e-mail for the specified user
     *
     * @param Authenticatable $user
     */
    public function sendActivationEmail(Authenticatable $user);

    /**
     * Log in or create a new account using an OAuth interface
     *
     * @param string      $driver
     * @param string|null $code
     *
     * @return \Symfony\Component\HttpFoundation\Response|Authenticatable
     */
    public function oauth($driver, $code = null);

}