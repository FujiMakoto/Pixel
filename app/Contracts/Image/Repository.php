<?php namespace Pixel\Contracts\Image;

use Pixel\Services\Image\Collection;

interface Repository {

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
     * Remove the soft-delete flag on an image record
     *
     * @param $id
     *
     * @return mixed
     */
    public function approve($id);

    /**
     * Unapprove (soft-delete) an image record
     *
     * @param $id
     *
     * @return mixed
     */
    public function unapprove($id);

    /**
     * Permanently delete an image
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);

}