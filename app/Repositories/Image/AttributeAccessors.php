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
                return config('image.scaling.preview.preserve_format')
                    ? $type
                    : 'jpg';

            case self::THUMBNAIL:
                return config('image.scaling.thumbnail.preserve_format')
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
     * Get the color scheme for this image
     *
     * @return string
     */
    public function getColorScheme()
    {
        return \ColorScheme::getClosest($this)->name;
    }

    /**
     * Get the maximum cache lifetime for this resource in seconds
     *
     * @return int
     */
    public function getMaxAge()
    {
        // Get the default max days
        $defaultDays = config('image.cache-control.max-age');
        $maxAge      = $defaultDays * 86400;

        // Is this image scheduled to expire at a specific date?
        if ( $expires = $this->getAttribute('expires') )
        {
            // In how many days will this image expire?
            $carbon  = \Carbon\Carbon::now();
            $expires = $this->asDateTime($expires);

            // Will the image expire before our default maximum cache lifetime?
            if ( $expires->lt($carbon->addDays($defaultDays)) )
                $maxAge = $expires->diffInSeconds($carbon->now());
        }

        return $maxAge;
    }

    /**
     * Get the raw URL to the image resource
     *
     * @param null|string $scale
     * @param array       $localParams
     *
     * @return string|null
     */
    public function getUrl($scale = null, array $localParams = [])
    {
        // Get our configured filesystem
        $fileSystem = config('filesystems.default');

        // Set parameters for local routes
        $localParams['sidFile'] = $this->getSidFilename();

        switch ($scale) {
            case self::PREVIEW:
                $localParams['size'] = 'preview';
                break;

            case self::THUMBNAIL:
                $localParams['size'] = 'thumbnail';
                break;
        }

        // If we're using the local filesystem, return our download route
        if ($fileSystem == 'local')
            return route('images.download', $localParams);

        // If we're using Amazon S3, return a direct link to the image
        if ($fileSystem == 's3') {
            $bucket   = config('filesystems.disks.s3.bucket');
            $filePath = $this->getRealPath($scale);
            return "https://s3.amazonaws.com/{$bucket}/{$filePath}";
        }

        // Unknown filesystem, return null
        return null;
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
        $previewWidth    = config('image.scaling.preview.width');
        $previewHeight   = config('image.scaling.preview.height');
        $thumbnailWidth  = config('image.scaling.thumbnail.width');
        $thumbnailHeight = config('image.scaling.thumbnail.height');

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