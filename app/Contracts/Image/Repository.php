<?php namespace Pixel\Contracts\Image;

use Pixel\Services\Image\Collection;

interface Repository {

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
     * @return mixed
     */
    public function getBySid($sid);

    /**
     * Retrieve an image by its primary key
     *
     * @param $id
     *
     * @return mixed
     */
    public function getById($id);

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
     * @param int  $perPage
     * @param bool $withExpired
     * @param bool $withInvisible
     *
     * @return mixed
     */
    public function recent($perPage = 12, $withExpired = false, $withInvisible = false);

    /**
     * Create a new image record
     *
     * @param $params
     *
     * @return mixed
     */
    public function create($params);

    /**
     * Update an image record
     *
     * @return bool
     */
    public function save();

    /**
     * Permanently delete an image
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);

    /**
     * Get the absolute system path to an image
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

}