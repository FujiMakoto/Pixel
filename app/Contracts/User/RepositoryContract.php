<?php namespace Pixel\Contracts\User;

use Illuminate\Contracts\Auth\Authenticatable;

interface RepositoryContract extends Authenticatable, Activatable {

    /**
     * Create a new user
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function create(array $attributes);

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