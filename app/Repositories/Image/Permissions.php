<?php namespace Pixel\Repositories\Image;

use Auth;
use Session;

trait Permissions {

    /**
     * Do we have permission to edit this image?
     *
     * @return bool
     */
    public function canEdit()
    {
        // Are we logged in?
        if ( Auth::check() )
        {
            // Is this image owned by us?
            if ( Auth::id() === $this->attributes['user_id'] )
                return true;

            // Are we an administrator or moderator?
            if ( Auth::user()->isAdmin() || Auth::user()->isModerator() )
                return true;
        }

        // Was this image uploaded by a guest?
        if ( ! $this->attributes['user_id'])
        {
            // Do we have an ownership flag for this image in our session?
            if ( in_array( $this->attributes['id'], Session::get('owned_images', []) ) )
                return true;
        }

        // All checks failed, so we don't have permission to edit this image
        return false;
    }

    /**
     * Make sure our specified delete key matches our image's
     *
     * @param string $deleteKey
     *
     * @return bool
     */
    public function checkDeleteKey($deleteKey)
    {
        // First make sure this image actually has a delete key
        if ( ! $this->attributes['delete_key'] || $this->attributes['user_id'])
            return false;

        // Does our delete key match?
        return ($deleteKey == $this->attributes['delete_key']);
    }

}