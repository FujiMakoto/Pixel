<?php namespace Pixel\Contracts\Image;

use Illuminate\Contracts\Auth\Guard;
use Pixel\Services\Image\Collection;
use Carbon\Carbon;

interface RepositoryContract {

    /**
     * Scale constants
     */
    const ORIGINAL  = null;
    const PREVIEW   = 'previews/';
    const THUMBNAIL = 'thumbnails/';

    /**
     * Retrieve an image by its string identifier
     *
     * @param $sid
     *
     * @return this
     * @throws ImageNotFoundException
     */
    public function getBySid($sid);

    /**
     * Retrieve an image by its primary key
     *
     * @param $id
     *
     * @return this
     * @throws ImageNotFoundException
     */
    public function getById($id);

    /**
     * Fetch all image entries matching the specified md5sum
     *
     * @param string      $md5sum
     * @param Carbon|null $date
     *
     * @return Collection
     */
    public function getByMd5($md5sum, $date = null);

    /**
     * Retrieve images posted by the specified user
     *
     * @param $user
     *
     * @return mixed
     */
    public function getByUser($user);

    /**
     * Retrieve images posted to the specified album
     *
     * @param $album
     *
     * @return mixed
     */
    public function getByAlbum($album);

    /**
     * Retrieve recently posted images
     *
     * @param int  $page
     * @param int  $perPage
     * @param bool $withExpired
     * @param bool $withInvisible
     *
     * @return mixed
     * @throws ImageNotFoundException
     */
    public function recent($page = 1, $perPage = 12, $withExpired = false, $withInvisible = false);

    /**
     * Create a new image record
     *
     * @param array $input
     *
     * @return this
     */
    public function create($input);

    /**
     * Update an image resource
     *
     * @return bool
     * @throws ImageNotFoundException
     */
    public function save();

    /**
     * Permanently delete an image
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Convert bytes to human readable format
     *
     * @param int $precision bytes
     *
     * @return string
     */
    public function getFilesize($precision = 2);

    /**
     * Get the file type for this image resource
     *
     * @param null|string $scale
     *
     * @return string
     */
    public function getType($scale = null);

    /**
     * Get the pseudo string identifier filename for HTTP requests
     *
     * @param null $scale
     *
     * @return string
     */
    public function getSidFilename($scale = null);

    /**
     * Get the color scheme for this image
     *
     * @return string
     */
    public function getColorScheme();

    /**
     * Get the raw URL to the image resource
     *
     * @param null|string $scale
     * @param array       $localParams
     *
     * @return string|null
     */
    public function getUrl($scale = null, array $localParams = []);

    /**
     * Get the absolute path to an image
     *
     * @param null|string $scale
     *
     * @return string
     */
    public function getRealPath($scale = null);

    /**
     * Get the base path to this image resource
     *
     * @param null|string $scale
     *
     * @return bool|string
     */
    public function getBasePath($scale = null);

    /**
     * Do we have permission to edit this image?
     *
     * @return bool
     */
    public function canEdit();

    /**
     * Make sure our specified delete key matches our image's
     *
     * @param string $deleteKey
     *
     * @return bool
     */
    public function checkDeleteKey($deleteKey);

}