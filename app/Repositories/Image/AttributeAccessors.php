<?php namespace Pixel\Repositories\Image;

/**
 * Class AttributeAccessors
 * @package Pixel\Repositories\Image
 */
trait AttributeAccessors {

    /**
     * Convert bytes to human readable format
     *
     * @param int $precision bytes
     *
     * @return string
     */
    public function getFilesize($precision = 2)
    {
        $bytes    = $this->attributes['size'];
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';

        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KiB';

        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MiB';

        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GiB';

        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TiB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get the file type for this image resource
     *
     * @param null|string $scale
     *
     * @return string
     */
    public function getType($scale = null)
    {
        $type = $this->attributes['type'];

        switch ($scale) {
            case self::PREVIEW:
                return config('pixel.scaling.preview.preserve_format')
                    ? $type
                    : 'jpg';

            case self::THUMBNAIL:
                return config('pixel.scaling.thumbnail.preserve_format')
                    ? $type
                    : 'jpg';

            default:
                return $type;
        }
    }

    /**
     * Get the pseudo string identifier filename for HTTP requests
     *
     * @param null $scale
     *
     * @return string
     */
    public function getSidFilename($scale = null)
    {
        // Get the string identifier and type attributes
        $sid  = $this->attributes['sid'];
        $type = $this->getType($scale);

        return $sid.'.'.$type;
    }

    /**
     * Get the absolute path to an image
     *
     * @param null|string $scale
     *
     * @return string
     */
    public function getRealPath($scale = null)
    {
        $basePath = $this->getBasePath();
        $md5sum   = $this->attributes['md5sum'];
        $type     = $this->getType($scale);

        // Define the width / height attributes
        $imageWidth      = $this->attributes['width'];
        $imageHeight     = $this->attributes['height'];
        $previewWidth    = config('pixel.scaling.preview.width');
        $previewHeight   = config('pixel.scaling.preview.height');
        $thumbnailWidth  = config('pixel.scaling.thumbnail.width');
        $thumbnailHeight = config('pixel.scaling.thumbnail.height');

        // Return the preview image only if our original has a scaled preview
        if ($scale == self::PREVIEW)
        {
            if ( ($imageWidth > $previewWidth) && ($imageHeight > $previewHeight) )
                return $basePath . self::PREVIEW . $md5sum.'.'.$type;
        };

        // Return the thumbnail image only if our original has a scaled thumbnail
        if ($scale == self::THUMBNAIL)
        {
            if ( ($imageWidth > $thumbnailWidth) && ($imageHeight > $thumbnailHeight) )
                return $basePath . self::THUMBNAIL . $md5sum.'.'.$type;
        };

        // Return the original image
        return $basePath . self::ORIGINAL  . $md5sum.'.'.$type;
    }

    /**
     * Get the base path to this image resource
     *
     * @param null|string $scale
     *
     * @return bool|string
     */
    public function getBasePath($scale = null)
    {
        if ( isset($this->attributes['created_at']) ) {
            $createdAt = $this->asDateTime($this->attributes['created_at']);
            return "images/{$createdAt->year}/{$createdAt->month}/{$createdAt->day}/{$scale}";
        }

        return false;
    }

}