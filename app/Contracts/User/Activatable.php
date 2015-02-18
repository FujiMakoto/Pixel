<?php namespace Pixel\Contracts\User;

interface Activatable {

    /**
     * Activate a users account
     */
    public function activate();

    /**
     * Is this users account active?
     *
     * @return bool
     */
    public function isActive();

    /**
     * Get the token value for the activation session
     *
     * @return string
     */
    public function getActivationToken();

    /**
     * Set the token value for the activation session
     *
     * @param string $value
     */
    public function setActivationToken($value);

    /**
     * Make sure the supplied activation code is valid
     *
     * @param string $code
     *
     * @return bool
     */
    public function validateActivationCode($code);

    /**
     * Make sure the supplied activation token is valid
     *
     * @param string $token
     *
     * @return bool
     */
    public function validateActivationToken($token);

    /**
     * Get the column name for the activation token
     *
     * @return string
     */
    public function getActivationTokenName();

    /**
     * Get the column name for the activation code
     *
     * @return string
     */
    public function getActivationCodeName();

}