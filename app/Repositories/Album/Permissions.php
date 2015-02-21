<?php namespace Pixel\Repositories\Album;

use Auth;
use Session;

trait Permissions {

    /**
     * Do we have permission to edit and upload images to this album?
     *
     * @return bool
     */
    public function canEdit()
    {
        // Are we logged in?
        if ( Auth::check() )
        {
            // Is this album owned by us?
            if ( Auth::id() === $this->getAttribute('user_id') )
                return true;

            // Are we an administrator or moderator?
            if ( Auth::user()->isAdmin() || Auth::user()->isModerator() )
                return true;
        }

        // Was this album created by a guest?
        if ( ! $this->getAttribute('user_id'))
        {
            // Do we have an ownership flag for this album in our session?
            if ( in_array( $this->getAttribute('id'), Session::get('owned.albums', []) ) )
                return true;
        }

        // All checks failed, so we don't have permission to edit this album
        return false;
    }

}