<?php namespace Pixel\Repositories\Image;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\Paginator;
use Pixel\Contracts\Image\Repository as RepositoryContract;
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
     * @return Collection
     */
    public function getBySid($sid)
    {
        $image = ImageModel::whereSid($sid)->firstOrFail()->toArray();
        $this->fill($image);

        return $this;
    }

    /**
     * Retrieve an image by its primary key
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        $image = ImageModel::findOrFail($id)->toArray();
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
     */
    public function recent($page = 1, $perPage = 12, $withExpired = false, $withInvisible = false)
    {
        // Set up a new Collection instance and get our recent listings
        $collection = new Collection();
        $offset     = $perPage * abs(($page - 1));
        $images     = ImageModel::orderBy('created_at', 'desc')->skip($offset)->take($perPage)->get();

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
     * @param $input
     *
     * @return mixed
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
     */
    public function save()
    {
        try {
            ImageModel::findOrFail($this->attributes['id'])->update($this->attributes);
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Remove the soft-delete flag on an image record
     *
     * @param $id
     *
     * @return mixed
     */
    public function approve($id)
    {
        $image = $this->getById($id);
        $image->restore();

        return true;
    }

    /**
     * Unapprove (soft-delete) an image record
     *
     * @param $id
     *
     * @return boolean
     */
    public function unapprove($id)
    {
        return ImageModel::destroy($id);
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