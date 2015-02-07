<?php namespace Pixel\Repositories\Image;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\Paginator;
use Pixel\Contracts\Image\Repository as RepositoryContract;
use Pixel\Exceptions\Image\ImageNotFoundException;
use Pixel\Repositories\Repository;
use Pixel\Repositories\Collection;
use Pixel\Image as ImageModel;

/**
 * Class DbRepository
 * @package Pixel\Repositories\Image
 */
class DbRepository extends Repository implements RepositoryContract {

    use AttributeAccessors;

    /**
     * Retrieve an image by its string identifier
     *
     * @param $sid
     *
     * @return this
     * @throws ImageNotFoundException
     */
    public function getBySid($sid)
    {
        try {
            $image = ImageModel::whereSid($sid)->firstOrFail()->toArray();
        } catch (ModelNotFoundException $e) {
            throw new ImageNotFoundException($e->getMessage(), $e->getCode());
        }
        $this->fill($image);

        return $this;
    }

    /**
     * Retrieve an image by its primary key
     *
     * @param $id
     *
     * @return this
     * @throws ImageNotFoundException
     */
    public function getById($id)
    {
        try {
            $image = ImageModel::findOrFail($id)->toArray();
        } catch (ModelNotFoundException $e) {
            throw new ImageNotFoundException($e->getMessage(), $e->getCode());
        }
        $this->fill($image);

        return $this;
    }

    /**
     * Retrieve images posted by the specified user
     *
     * @param $user
     *
     * @return mixed
     */
    public function getByUser($user)
    {
        // @todo: Replace with User repository
        $collection = new Collection();
        $userId     = ($user instanceof \Pixel\User) ? $user->id : intval($user);
        $images     = ImageModel::whereUserId($userId)->get();

        foreach ($images as $image)
            $collection->add( new static($image->toArray()) );

        return $collection;
    }

    /**
     * Retrieve images posted to the specified album
     *
     * @param $album
     *
     * @return mixed
     */
    public function getByAlbum($album)
    {
        // @todo: Replace with Album repository
        $collection = new Collection();
        $albumId    = ($album instanceof self) ? $album->id : intval($album);
        $images     = ImageModel::whereUserId($albumId)->get();

        foreach ($images as $image)
            $collection->add( new static($image->toArray()) );

        return $collection;
    }

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
    public function recent($page = 1, $perPage = 12, $withExpired = false, $withInvisible = false)
    {
        // Set up a new Collection instance and get our recent listings
        $collection = new Collection();
        $offset     = $perPage * abs(($page - 1));
        $images     = ImageModel::orderBy('created_at', 'desc')->skip($offset)->take($perPage)->get();

        // Did we get any results?
        if ( ! $images->count() )
            throw new ImageNotFoundException('No recent images found');

        foreach ($images as $image)
            $collection->add( new static($image->toArray()) );

        // Count the total number of listings for our previous query
        $total = ImageModel::count();

        // Set up pagination
        // @todo: Understand how pagination works in Laravel 5
        $pagination = new Paginator($collection, $perPage, $page, ['total' => $total]);
        return $pagination;
    }

    /**
     * Create a new image record
     *
     * @param array $input
     *
     * @return this
     */
    public function create($input)
    {
        $image = ImageModel::create($input)->toArray();
        $this->fill($image);

        return $this;
    }

    /**
     * Update an image resource
     *
     * @return bool
     * @throws ImageNotFoundException
     */
    public function save()
    {
        try {
            ImageModel::findOrFail($this->attributes['id'])->update($this->attributes);
            return true;
        } catch (ModelNotFoundException $e) {
            throw new ImageNotFoundException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Permanently delete an image
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id)
    {
        return ImageModel::destroy($id);
    }

}