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
     * Activate a users account
     *
     * @param Authenticatable $user
     * @param bool|string     $code
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

}