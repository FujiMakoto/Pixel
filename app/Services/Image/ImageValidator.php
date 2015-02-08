<?php namespace Pixel\Services\Image;

use Illuminate\Validation\Validator;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageValidator extends Validator {

    /**
     * Make sure an uploaded file is really an image file
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return bool
     */
    public function validateValidImage($attribute, $value, $parameters)
    {
        // Make sure we actually have a file to work with
        if ( ! $value instanceof UploadedFile || ! $value->isValid()) return false;

        # This appears to be one of the best methods to validate whether or not the file uploaded is a valid image.
        # It's not type specific and it's a native PHP function (doesn't require Imagick or even GD). If the image is
        # invalid, the first element in the returned array will be 0
        $dimensions = getimagesize( $value->getRealPath() );

        if ( ! isset($dimensions[0]) || ! $dimensions[0]) return false;

        # There are still situations in which the above will fail. If we're using Imagick, we can perform
        # further validation on the uploaded image.
        if ( config('pixel.driver') == 'imagick' )
        {
            try {
                new \Imagick( $value->getRealPath() );
            } catch (\ImagickException $e) {
                return false;
            }
        }

        return true;
    }

}