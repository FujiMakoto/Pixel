<?php namespace Pixel\Services\Image;

use Pixel\Contracts\Image\Repository;
use Pixel\Exceptions\Image\UnreadableImageException;
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
     * Create scaled versions of an image resource
     *
     * @param Repository $image
     *
     * @return boolean
     */
    public function createScaledImages(Repository $image)
    {
        // Create the preview image
        $previewConfig = config('pixel.scaling.preview');
        $this->scaleImage($image::PREVIEW, $previewConfig, $image);

        // Create the thumbnail image
        $thumbnailConfig = config('pixel.scaling.thumbnail');
        $this->scaleImage($image::THUMBNAIL, $thumbnailConfig, $image);

        return true;
    }

    /**
     * Get information about an image file
     *
     * @param UploadedFile $file
     *
     * @return array
     * @throws UnreadableImageException
     */
    protected function getImageData(UploadedFile $file)
    {
        // Get the file path
        $filePath = $file->getRealPath();

        // Create an imagick instance or throw an exception if we can't read the image
        try {
            $imagick = new \Imagick($filePath);
        } catch (\ImagickException $e) {
            throw new UnreadableImageException($e->getMessage(), $e->getCode());
        }

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

    /**
     * Perform scaling on an image
     *
     * @param string     $scale
     * @param array      $config
     * @param Repository $image
     *
     * @return bool
     * @throws UnreadableImageException
     */
    private function scaleImage($scale, $config, Repository $image)
    {
        // Make sure our configuration is valid
        if ( ! isset($config['quality'], $config['width'], $config['height']) ) {
            return false;
        }

        // Is the image already smaller than what we're trying to scale it to?
        if ( ($image->width <= $config['width']) && ($image->height <= $config['height']) ) {
            return false;
        }

        // Get the file contents
        $filePath     = $image->getRealPath($image::ORIGINAL);
        $fileContents = $this->filesystem->get($filePath);

        // Instantiate a new Imagick instance
        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($fileContents);
        } catch (\ImagickException $e) {
            throw new UnreadableImageException($e->getMessage(), $e->getCode());
        }

        // Get and make sure our base filesystem path exists
        $scalePath = $image->getBasePath($scale);
        if ( ! $this->filesystem->exists($scalePath) )
            $this->filesystem->makeDirectory($scalePath);

        // Are we encoding the preview in JPEG format?
        $scaleType = $image->type;
        if ( ($scaleType == self::JPEG) || ! $config['preserve_format'] ) {
            $scaleType = self::JPEG;
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality($config['quality']);
        }

        // Define the default Imagick parameters
        $imagick->setImageInterlaceScheme(\Imagick::INTERLACE_LINE);
        $imagick->setSamplingFactors([1,1,1]);

        // Scale or crop the image
        if ($config['method'] == 'scale') {
            $imagick->scaleImage($config['width'], $config['height'], true);
        } else {
            $imagick->cropThumbnailImage($config['width'], $config['height']);
        }

        // Save our scaled image to the filesystem
        $scaleName = $image->md5sum.'.'.$scaleType;
        $status = $this->filesystem->put($scalePath.$scaleName, $imagick->getImageBlob()); // @todo: throw exception

        // Clean up and return
        $imagick->clear();
        return $status;
    }


}