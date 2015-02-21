<?php namespace Pixel\Services\Album;

use Pixel\Contracts\Album\AlbumContract;
use Pixel\Contracts\Album\RepositoryContract;
use Pixel\Exceptions\Album\AlbumNotFoundException;

class AlbumService implements AlbumContract {

    /**
     * @var RepositoryContract
     */
    protected $albumRepo;

    /**
     * Constructor
     *
     * @param RepositoryContract $albumRepo
     */
    public function __construct(RepositoryContract $albumRepo)
    {
        $this->albumRepo = $albumRepo;
    }

    /**
     * Determine if an album exists
     *
     * @param string $sid
     *
     * @return boolean
     */
    public function exists($sid)
    {
        try {
            $this->albumRepo->getBySid($sid);
        } catch (AlbumNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve an album
     *
     * @param string $sid
     *
     * @return RepositoryContract
     */
    public function get($sid)
    {
        return $this->albumRepo->getBySid($sid);
    }

    /**
     * Retrieve an album by its primary key
     *
     * @param int $id
     *
     * @return RepositoryContract
     */
    public function getById($id)
    {
        return $this->albumRepo->getById($id);
    }

    /**
     * Retrieve albums created by the specified user
     *
     * @param $user
     *
     * @return mixed
     */
    public function getByUser($user)
    {
        // TODO: Implement getByUser() method.
    }

    /**
     * Create a new album
     *
     * @param array $attributes
     *
     * @return RepositoryContract
     */
    public function create(array $attributes)
    {
        // Set some additional attributes
        $attributes['sid']           = str_random(7);
        $attributes['user_id']       = \Auth::check() ? \Auth::id() : 0;
        $attributes['upload_ip']     = \Request::ip();
        $attributes['upload_uagent'] = \Request::server('HTTP_USER_AGENT');

        // Make sure an album with this string identifier doesn't exist already
        while ($this->exists($attributes['sid'])) {
            $attributes['sid'] = str_random(7);
        }

        // Create the album in our repository
        $album = $this->albumRepo->create($attributes);

        // If we're a guest, set an ownership flag in our session
        if ( \Auth::guest() )
            \Session::push('owned.albums', $album->id);

        // Return our album repository
        return $album;
    }

}