<?php namespace Pixel\Services\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImagickDriver extends ImageService {

    /**
     * Crop an image to the specified dimensions
     *
     * @param $image
     * @param $params
     *
     * @return mixed
     */
    public function crop($image, $params)
    {
        // TODO: Implement crop() method.
    }

    /**
     * Convert an image to the specified filetype
     *
     * @param $image
     * @param $filetype
     *
     * @return mixed
     */
    public function convert($image, $filetype)
    {
        // TODO: Implement convert() method.
    }

    /**
     * Get information about an image file
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    public function getImageData(UploadedFile $file)
    {
        // Get the file match and create a new Imagick instance
        $filePath = $file->getRealPath();
        $imagick  = new \Imagick($filePath);

        // Define an array of image data
        $data['md5sum'] = md5_file($filePath);
        $data['name']   = $file->getClientOriginalName();
        $data['size']   = $file->getSize();
        $data['width']  = $imagick->getImageWidth();
        $data['height'] = $imagick->getImageHeight();

        // Define the image type
        switch ( $file->getClientOriginalExtension() ) {
            case self::JPEG:
                $data['type'] = self::JPEG;
                break;

            case self::PNG:
                $data['type'] = self::PNG;
                break;

            case self::GIF:
                $data['type'] = self::GIF;
                break;

            default:
                $data['type'] = self::JPEG;
        }

        return $data;
    }


}