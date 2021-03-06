<?php namespace Pixel\Services\Image;

use Illuminate\Contracts\Filesystem\Filesystem;
use Pixel\Contracts\Image\ImageContract;
use Pixel\Contracts\Image\RepositoryContract;
use Pixel\Exceptions\Image\ImageNotFoundException;
use Pixel\Exceptions\Image\InvalidConfigurationException;
use Pixel\Exceptions\Image\UnsupportedFilesystemException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Guzzle\Http\Mimetypes;
use ColorThief\ColorThief;
use Carbon\Carbon;
use SplFileObject;

/**
 * Class ImageService
 * @package Pixel\Services\Image
 */
abstract class ImageService implements ImageContract {

    /**
     * XSendfile header constants
     */
    const XSENDFILE_NGINX    = 'X-Accel-Redirect';
    const XSENDFILE_APACHE   = 'X-Sendfile';
    const XSENDFILE_LIGHTTPD = 'X-LIGHTTPD-send-file';

    /**
     * XSendfile server headers
     *
     * @var array
     */
    protected $xsendfileHeaders = [
        'nginx'    => self::XSENDFILE_NGINX,
        'apache'   => self::XSENDFILE_APACHE,
        'lighttpd' => self::XSENDFILE_LIGHTTPD
    ];

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
        try {
            $this->imageRepo->getBySid($sid);
        } catch (ImageNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve an image
     *
     * @param string $sid
     *
     * @return RepositoryContract
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
     * @return RepositoryContract
     */
    public function getById($id)
    {
        return $this->imageRepo->getById($id);
    }

    /**
     * Fetch all image entries matching the specified md5sum
     *
     * @param string      $md5sum
     * @param Carbon|null $date
     *
     * @return Collection
     */
    public function getByMd5($md5sum, $date = null)
    {
        return $this->imageRepo->getByMd5($md5sum, $date);
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
        return $this->imageRepo->getByAlbum($album);
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
     * @return RepositoryContract
     */
    public function create(UploadedFile $file, array $params = [])
    {
        $imageData     = $this->getImageData($file);
        $dominantColor = $this->getImageDominantColor( $file->getRealPath() );

        // Set some additional fields
        $params['sid']             = str_random('7');
        $params['delete_key']      = \Auth::guest() ? str_random('40') : null;
        $params['original_width']  = $imageData['width'];
        $params['original_height'] = $imageData['height'];
        $params['user_id']         = \Auth::check() ? \Auth::id() : 0;
        $params['upload_ip']       = \Request::getClientIp();
        $params['upload_uagent']   = \Request::server('HTTP_USER_AGENT');

        // Perform some sanity checks if we're uploading to an album
        if ( isset($params['album_id']) ) {
            $album = \Album::getById($params['album_id']);

            // Make sure we have upload access to this album
            if ( ! $album->canEdit() )
                unset($params['album_id']);
        } else {
            unset($params['album_id']); // album_id is possibly set but null
        }

        // Concatenate our parameters
        $params = array_merge($imageData, $dominantColor, $params);

        // Make sure an image with this string identifier doesn't exist already
        while ($this->exists($params['sid'])) {
            $params['sid'] = str_random('7');
        }

        // Create the image in our repository
        $image = $this->imageRepo->create($params);

        // If we're a guest, set an ownership flag in our session
        if ( \Auth::guest() )
            \Session::push('owned_images', $image->id);

        # Images are saved in YYYY/MM/DD sub-directories with md5sum's as file names.
        # This is useful in multiple ways, it avoids wasting storage space by saving multiple
        # copies of the same file and keeps an organized and human readable filesystem heirarchy.

        // Make sure our filesystem path exists
        $path = $image->getBasePath();
        if ( ! $this->filesystem->exists($path) )
            $this->filesystem->makeDirectory($path);

        // Get the file contents and save them to our configured filesystem
        $filename     = "{$image->md5sum}.{$image->type}";
        $fileContents = file_get_contents( $file->getRealPath() );
        $this->filesystem->put($path.$filename, $fileContents);

        // Create the scaled (preview/thumbnail) versions of our new image
        $this->createScaledImages($image);

        // Return our image collection
        return $image;
    }

    /**
     * Crop an image to the specified coordinates
     *
     * @param RepositoryContract $image
     * @param array              $coords
     *
     * @return RepositoryContract
     * @throws UnreadableImageException
     */
    abstract public function crop(RepositoryContract $image, array $coords);

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
     * @param Repository|int $image
     *
     * @return bool
     */
    public function delete($image)
    {
        // Make sure $image is a Repository instance
        if ( ! $image instanceof RepositoryContract)
            $image = $this->get($image);

        // Set our filesystem paths
        $paths['original']  = $image->getRealPath($image::ORIGINAL);
        $paths['preview']   = $image->getRealPath($image::PREVIEW);
        $paths['thumbnail'] = $image->getRealPath($image::THUMBNAIL);

        // Make sure this is our only remaining entry for this image
        $copies = $this->getByMd5($image->md5sum, $image->created_at);

        // Loop through our paths and delete the files
        if ($copies->count() < 2) {
            foreach ($paths as $path) {
                if ($this->filesystem->exists($path))
                    $this->filesystem->delete($path);
            }
        }

        // Delete the image from our backend
        $image->delete($image->id);

        // Return success
        return true;
    }

    /**
     * Get the dominant color used in an image
     *
     * @param string $filePath
     *
     * @return array|bool
     */
    public function getImageDominantColor($filePath)
    {
        // Set up an instance of Color Thief
        $colorThief = new ColorThief();
        $pallet = $colorThief->getColor($filePath, 8);

        // Successful result, return the color pallet
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
    public function downloadResponse(RepositoryContract $image, $scale = null, $disposition = 'inline')
    {
        $fileSystem = config('filesystems.default');

        // Standard filesystem response
        if ( $fileSystem == 'local' ) {
            // Get our file path and cache headers
            $filePath     = config('filesystems.disks.local.root').'/'.$image->getRealPath($scale);
            $cacheHeaders = $this->getCacheHeaders($image);

            // Are we utilizing XSendfile?
            if ( config('filesystems.disks.local.sendfile.enabled') )
            {
                // Get our sendfile headers and merge them with our cache headers
                $xsendfileHeaders = $this->getSendfileHeaders($image, $scale);
                $headers = array_merge($xsendfileHeaders->all(), $cacheHeaders->all()); // cacheHeaders must be last

                // Return our XSendfile response
                return response(null, 200, $headers);
            }

            // Return a standard PHP download response
            $fileObject = new SplFileObject($filePath);
            return response()->download($fileObject, $image->name, $cacheHeaders->all(), $disposition);
        }

        // Amazon S3 response
        if ( $fileSystem == 's3' ) {
            // Generate a URL to the image on our S3 bucket
            $bucket   = config('filesystems.disks.s3.bucket');
            $filePath = $image->getRealPath($scale);
            $imageUrl = "https://s3.amazonaws.com/{$bucket}/{$filePath}";

            // Return a temporary redirect
            return response()->redirectTo($imageUrl);
        }

        throw new UnsupportedFilesystemException("{$fileSystem} is not a supported Filesystem");
    }

    /**
     * Retrieve the cache headers for this image resource
     *
     * @param RepositoryContract $image
     *
     * @return ResponseHeaderBag
     */
    public function getCacheHeaders(RepositoryContract $image)
    {
        // Set up the header bag and get our images max cache lifetime
        $headers = new ResponseHeaderBag();
        $maxAge  = $image->getMaxAge();

        // Define our cache control and expires headers
        $headers->addCacheControlDirective('max-age', $maxAge);
        if ( config('image.cache-control.no-transform') )
            $headers->addCacheControlDirective('no-transform', true);
        $headers->addCacheControlDirective('public', true);
        $headers->add(['Expires' => Carbon::now()->addSeconds($maxAge)->toRfc1123String()]);

        // Set the Last-Modified header according to our updated_at attribute
        $headers->add(['Last-Modified' => $image->updated_at->toRfc1123String()]);

        return $headers;
    }

    /**
     * Retrieve the XSendfile headers for this image resource
     *
     * @param RepositoryContract $image
     * @param string|null        $scale
     *
     * @return ResponseHeaderBag
     * @throws InvalidConfigurationException
     */
    public function getSendfileHeaders(RepositoryContract $image, $scale = null)
    {
        // Set up the header bag and get our sendfile configuration
        $config    = config('filesystems.disks.local.sendfile');
        $imagePath = $image->getRealPath($scale);

        // Make sure we have a valid server configured
        if ( ! isset($this->xsendfileHeaders[ $config['server'] ]) )
            throw new InvalidConfigurationException("\"{$config['server']}\" is not a supported XSendfile server");

        // Define our header key and value attributes
        $headerKey   = $this->xsendfileHeaders[ $config['server'] ];
        $headerValue = $config['path'].'/'.$imagePath;
        $contentType = Mimetypes::getInstance()->fromFilename($imagePath);

        // Set and return our XSendfile header
        return new ResponseHeaderBag([
            $headerKey     => $headerValue,
            'Content-Type' => $contentType
        ]);
    }

    /**
     * Create scaled versions of an image resource
     *
     * @param RepositoryContract $image
     *
     * @return bool
     */
    abstract public function createScaledImages(RepositoryContract $image);

    /**
     * Get information about an uploaded image file
     *
     * @param UploadedFile $file
     *
     * @return array
     */
    abstract protected function getImageData(UploadedFile $file);

}