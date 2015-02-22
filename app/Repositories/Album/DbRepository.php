<?php namespace Pixel\Repositories\Album;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Pixel\Contracts\Album\RepositoryContract;
use Pixel\Repositories\Collection;
use Pixel\Repositories\Repository;
use Pixel\Exceptions\Album\AlbumNotFoundException;
use Pixel\Album as AlbumModel;

class DbRepository extends Repository implements RepositoryContract {

    use Permissions;

    protected $images;

    /**
     * Retrieve an album by its string identifier
     *
     * @param $sid
     *
     * @return $this
     * @throws AlbumNotFoundException
     */
    public function getBySid($sid)
    {
        try {
            $album = AlbumModel::whereSid($sid)->firstOrFail()->toArray();
        } catch (ModelNotFoundException $e) {
            throw new AlbumNotFoundException($e->getMessage(), $e->getCode());
        }
        $this->fill($album);

        return $this;
    }

    /**
     * Retrieve an image by its primary key
     *
     * @param $id
     *
     * @return this
     * @throws AlbumNotFoundException
     */
    public function getById($id)
    {
        try {
            $album = AlbumModel::findOrFail($id)->toArray();
        } catch (ModelNotFoundException $e) {
            throw new AlbumNotFoundException($e->getMessage(), $e->getCode());
        }
        $this->fill($album);

        return $this;
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
     * Create a new album record
     *
     * @param array $attributes
     *
     * @return this
     */
    public function create($attributes)
    {
        $album = AlbumModel::create($attributes)->toArray();
        $this->fill($album);

        return $this;
    }

    /**
     * Update an album resource
     *
     * @return bool
     * @throws AlbumNotFoundException
     */
    public function save()
    {
        try {
            AlbumModel::findOrFail($this->getAttribute('id'))->update($this->getAllAttributes());
            return true;
        } catch (ModelNotFoundException $e) {
            throw new AlbumNotFoundException($e->getMessage(), $e->getCode());
        }

        return false;
    }

    /**
     * Permanently delete an album
     *
     * @param bool $includeImages
     *
     * @return bool
     */
    public function delete($includeImages = true)
    {
        if ($includeImages) {
            // TODO
        }

        return AlbumModel::destroy($this->getAttribute('id'));
    }

    /**
     * Get the primary color scheme for this album
     *
     * @param int $sampleLimit
     *
     * @return string
     */
    public function getColorScheme($sampleLimit = 25)
    {
        // Fetch some image samples from the album
        $images = $this->images()->take($sampleLimit);

        // Loop through each image and get its associated color scheme
        $schemes = [];
        foreach ($images as $image) {
            $name = $image->getColorScheme();
            if ( isset($schemes[$name]) ) { $schemes[$name]++; } else { $schemes[$name] = 1; };
        }

        // Find the most frequently used color scheme and return it
        return array_search(max($schemes), $schemes);
    }

    /**
     * Get images uploaded to this album
     *
     * @return Collection
     */
    public function images()
    {
        return ($this->images instanceof Collection)
            ? $this->images
            : $this->images = \Image::getByAlbum($this);
    }

}