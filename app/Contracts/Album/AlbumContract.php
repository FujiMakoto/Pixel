<?php namespace Pixel\Contracts\Album;


interface AlbumContract {

    /**
     * Determine if an album exists
     *
     * @param string $sid
     *
     * @return boolean
     */
    public function exists($sid);

    /**
     * Retrieve an album
     *
     * @param string $sid
     *
     * @return RepositoryContract
     */
    public function get($sid);

    /**
     * Retrieve an album by its primary key
     *
     * @param int $id
     *
     * @return RepositoryContract
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
     * Create a new album
     *
     * @param array $attributes
     *
     * @return RepositoryContract
     */
    public function create(array $attributes);

}