<?php namespace Pixel\Repositories\User;

use Auth;

trait Permissions {

    /**
     * Is this user an administrator?
     *
     * @return bool
     */
    public function isAdmin()
    {
        // If we're not logged in, we're obviously not an admin
        if ( Auth::guest() )
            return false;

        return $this->attributes['is_admin'];
    }

    /**
     * Is this user a moderator?
     *
     * @return bool
     */
    public function isModerator()
    {
        // If we're not logged in, we're obviously not an admin
        if ( Auth::guest() )
            return false;

        return $this->attributes['is_moderator'];
    }

    /**
     * Is this users account active?
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->attributes['active'];
    }

}