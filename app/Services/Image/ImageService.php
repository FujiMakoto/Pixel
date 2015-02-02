<?php namespace Pixel\Services\Image;

use Pixel\Contracts\Image\ImageContract as ImageContract;
use Pixel\Contracts\Image\Repository as RepositoryContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ColorThief\ColorThief;

/**
 * Class ImageService
 * @package Pixel\Services\Image
 */
abstract class ImageService implements ImageContract {

    /**
     * @var ImageRepository
     */
    protected $imageRepo;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param RepositoryContract $imageRepo
     * @param Filesystem         $filesystem
     */
    public function __construct(RepositoryContract $imageRepo, Filesystem $filesystem)
    {
        $this->imageRepo  = $imageRepo;
        $this->filesystem = $filesystem;
    }

    /**
     * Determine if an image exists
     *
     * @param string $sid
     *
     * @return boolean
     */
    public function exists($sid)
    {
        if ( $this->imageRepo->getBySid($sid) )
            return true;

        return false;
    }

    /**
     * Retrieve an image
     *
     * @param string $sid
     *
     * @return mixed
     */
    public function get($sid)
    {
        return $this->imageRepo->getBySid($sid);
    }

    /**
     * Retrieve an image by its primary key
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->imageRepo->getById($id);
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
        //
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
        //
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
        return $this->imageRepo->recent($page, $perPage, $withExpired, $withInvisible);
    }

    /**
     * Save a new image
     *
     * @param UploadedFile $file
     * @param array        $params
     *
     * @return mixed
     */
    public function create(UploadedFile $file, array $params = [])
    {
        $imageData     = $this->getImageData($file);
        $dominantColor = $this->getImageDominantColor($file);

        // Set some additional fields
        $params['sid']             = str_random('7');
        $params['delete_key']      = str_random('40');
        $params['original_width']  = $imageData['width'];
        $params['original_height'] = $imageData['height'];
        $params['upload_ip']       = \Request::getClientIp();
        $params['upload_uagent']   = \Request::server('HTTP_USER_AGENT');

        // Concatenate our parameters
        $params = array_merge($imageData, $dominantColor, $params);

        // Create the image in our repository
        $image = $this->imageRepo->create($params);

        # Images are saved in YYYY/MM/DD sub-directories with md5sum's as file names.
        # This is useful in multiple ways, it avoids wasting storage space by saving multiple
        # copies of the same file and keeps an organized and human readable filesystem heirarchy.

        // Make sure our filesystem path exists
        $createdAt = $image->created_at;
        $path = "images/{$createdAt->year}/{$createdAt->month}/{$createdAt->day}/";
        if ( ! $this->filesystem->exists($path) )
            $this->filesystem->makeDirectory($path);

        // Get the file contents and save them to our configured filesystem
        $filename     = "{$image->md5sum}.{$image->type}";
        $fileContents = file_get_contents( $file->getRealPath() );
        $this->filesystem->put($path.$filename, $fileContents);

        // Return our image collection
        return $image;
    }

    /**
     * Crop an image to the specified dimensions
     *
     * @param $image
     * @param $params
     *
     * @return mixed
     */
    abstract public function crop($image, $params);

    /**
     * Convert an image to the specified filetype
     *
     * @param $image
     * @param $filetype
     *
     * @return mixed
     */
    abstract public function convert($image, $filetype);

    /**
     * Delete an image
     *
     * @param $image
     *
     * @return mixed
     */
    public function delete($image)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Get information about an image file
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    abstract public function getImageData(UploadedFile $file);

    /**
     * Get the dominant color used in an image
     *
     * @param UploadedFile $file
     *
     * @return array|bool
     */
    public function getImageDominantColor(UploadedFile $file)
    {
        // Set up an instance of Color Thief
        $colorThief = new ColorThief();
        $pallet = $colorThief->getColor($file->getRealPath(), 8);

        // Successful result, set the color pallet
        if ( isset($pallet[0], $pallet[1], $pallet[2]) ) {
            return [
                'red'   => $pallet[0],
                'green' => $pallet[1],
                'blue'  => $pallet[2]
            ];
        }

        // Failed to get a dominant color, return null
        return [
            'red'   => null,
            'green' => null,
            'blue'  => null
        ];
    }

}