<?php namespace Pixel\Contracts\Album;

interface RepositoryContract {

    /**
     * Retrieve an album by its string identifier
     *
     * @param $sid
     *
     * @return this
     * @throws AlbumNotFoundException
     */
    public function getBySid($sid);

    /**
     * Retrieve an image by its primary key
     *
     * @param $id
     *
     * @return this
     * @throws AlbumNotFoundException
     */
    public function getById($id);

    /**
     * Retrieve albums created by the specified user
     *
     * @param $user
     *
     * @return mixed
     */
    public function getByUser($user);

    /**
     * Create a new album record
     *
     * @param array $attributes
     *
     * @return this
     */
    public function create($attributes);

    /**
     * Update an album resource
     *
     * @return bool
     * @throws AlbumNotFoundException
     */
    public function save();

    /**
     * Permanently delete an album
     *
     * @param bool $includeImages
     *
     * @return bool
     */
    public function delete($includeImages = true);

    /**
     * Get the primary color scheme for this album
     *
     * @param int $sampleLimit
     *
     * @return string
     */
    public function getColorScheme($sampleLimit = 25);

    /**
     * Do we have permission to edit and upload images to this album?
     *
     * @return bool
     */
    public function canEdit();

}