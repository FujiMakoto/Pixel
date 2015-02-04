<?php namespace Pixel\Contracts\Image;

use Pixel\Services\Image\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @param $sid
     *
     * @return mixed
     */
    public function exists($sid);

    /**
     * Retrieve an image by its string identifier
     *
     * @param string $sid
     *
     * @return mixed
     */
    public function get($sid);

    /**
     * Retrieve an image by its primary key
     *
     * @param int $id
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
     * Save a new image
     *
     * @param UploadedFile $image
     * @param array        $params
     *
     * @return mixed
     */
    public function create(UploadedFile $image, array $params = []);

    /**
     * Crop an image to the specified dimensions
     *
     * @param $image
     * @param $params
     *
     * @return mixed
     */
    public function crop($image, $params);

    /**
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
     * @return mixed
     */
    public function delete($image);

    /**
     * Generate a download response for the specified image
     *
     * @param Repository  $image
     * @param string|null $scale
     * @param string      $disposition
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     * @throws UnsupportedFilesystemException
     */
    public function downloadResponse(Repository $image, $scale = null, $disposition = 'inline');

    /**
     * Get the dominant color used in an image
     *
     * @param string $filePath
     */
    public function getImageDominantColor($filePath);

    /**
     * Create scaled versions of an image resource
     *
     * @param Repository $image
     *
     * @return mixed
     */
    public function createScaledImages(Repository $image);

}