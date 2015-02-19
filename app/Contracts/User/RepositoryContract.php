<?php namespace Pixel\Contracts\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;

interface RepositoryContract extends Authenticatable, Activatable, CanResetPassword {

    /**
     * Create a new user
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function create(array $attributes);

    /**
     * Terminate the specified user
     */
    public function destroy();

    /**
     * Retrieve a user by their unique identifier
     *
     * @param int $id
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getById($id);

    /**
     * Retrieve a user by their OAuth credentials
     *
     * @param string $driver
     * @param int    $id
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getByOAuthId($driver, $id);

    /**
     * Retrieve a user by by their unique identifier and "remember me" token
     *
     * @param int     $id
     * @param string  $token
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getByToken($id, $token);

    /**
     * Retrieve a user by the given credentials
     *
     * @param array $credentials
     *
     * @return $this
     * @throws UserNotFoundException
     */
    public function getByCredentials(array $credentials);

    /**
     * Is this user an administrator?
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Is this user a moderator?
     *
     * @return bool
     */
    public function isModerator();

}