<?php namespace Pixel\Contracts\Image;

use Pixel\Services\Image\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Carbon\Carbon;

/**
 * Interface ImageContract
 * @package Pixel\Contracts\Image
 */
interface ImageContract {

    /**
     * Filetype constants
     */
    const JPEG = 'jpg';
    const PNG  = 'png';
    const GIF  = 'gif';

    /**
     * Determine if an image exists
     *
     * @param string $sid
     *
     * @return boolean
     */
    public function exists($sid);

    /**
     * Retrieve an image
     *
     * @param string $sid
     *
     * @return RepositoryContract
     */
    public function get($sid);

    /**
     * Retrieve an image by its primary key
     *
     * @param int $id
     *
     * @return RepositoryContract
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
     */
    public function recent($page = 1, $perPage = 12, $withExpired = false, $withInvisible = false);

    /**
     * Save a new image
     *
     * @param UploadedFile $image
     * @param array        $params
     *
     * @return RepositoryContract
     */
    public function create(UploadedFile $image, array $params = []);

    /**
     * Crop an image to the specified coordinates
     *
     * @param RepositoryContract $image
     * @param array              $coords
     *
     * @return RepositoryContract
     * @throws UnreadableImageException
     */
    public function crop(RepositoryContract $image, array $coords);

    /**
     * Convert an image to the specified filetype
     *
     * @param $image
     * @param $filetype
     *
     * @return mixed
     */
    public function convert($image, $filetype);

    /**
     * Delete an image
     *
     * @param $image
     *
     * @return bool
     */
    public function delete($image);

    /**
     * Get the dominant color used in an image
     *
     * @param string $filePath
     *
     * @return array|bool
     */
    public function getImageDominantColor($filePath);

    /**
     * Generate a download response for the specified image
     *
     * @param RepositoryContract $image
     * @param string|null        $scale
     * @param string             $disposition
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     * @throws UnsupportedFilesystemException
     */
    public function downloadResponse(RepositoryContract $image, $scale = null, $disposition = 'inline');

    /**
     * Retrieve the cache headers for this image resource
     *
     * @param RepositoryContract $image
     *
     * @return ResponseHeaderBag
     */
    public function getCacheHeaders(RepositoryContract $image);

    /**
     * Create scaled versions of an image resource
     *
     * @param RepositoryContract $image
     *
     * @return bool
     */
    public function createScaledImages(RepositoryContract $image);

}