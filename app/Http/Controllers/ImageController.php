<?php namespace Pixel\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Pixel\Http\Requests\ImageCropRequest;
use Pixel\Http\Requests\ImageUploadRequest;
use Pixel\Http\Requests\ImageDestroyRequest;
use Pixel\Contracts\Image\ImageContract;
use Illuminate\Http\Request;
use Response;
use Session;

/**
 * Class ImageController
 * @package Pixel\Http\Controllers
 */
class ImageController extends Controller {

	/**
	 * @var ImageContract
	 */
	protected $imageService;

	/**
	 * Constructor
	 *
	 * @param ImageContract $imageService
	 */
	public function __construct(ImageContract $imageService)
	{
		$this->imageService = $imageService;
	}

	/**
	 * Redirect to the homepage
	 * GET /images
	 *
	 * @return Response
	 */
	public function index()
	{
		return response()->redirectToRoute('home');
	}

	/**
	 * Show the image upload form
	 * GET /images/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('images/create');
	}

	/**
	 * Process the uploaded image
	 * POST /images
	 *
	 * @param ImageUploadRequest $request
	 *
	 * @return Response
	 */
	public function store(ImageUploadRequest $request)
	{
		// Create the image
		$image = $this->imageService->create( $request->file('image'), $request->only('album_id') );
        $image = $this->imageService->getById( $image->id ); // needed for loading relationships

		// Redirect to the newly created image resource
		if ( $request->ajax() ) {
			$response['templates']['imageDetails'] = view('images/_partials/details', ['image' => $image])->render();
			$response['templates']['toolbars']     = view('images/_partials/toolbars', ['image' => $image])->render();
            $response['header']['text']    = $image->name;
            $response['header']['subtext'] = $image->md5sum;
			$response['uploadUrl'] = route('images.show', ['sid' => $image->sid]);

			return response()->json($response);
		}

		//return response()->json(['redirect' => route('images.show', ['sid' => $image->sid])]);

		return response()->redirectToRoute('images.show', ['sid' => $image->sid]);
	}

	/**
	 * Display the preview page for the specified image
	 * GET /images/{sid}
	 *
	 * @param string $sid
	 *
	 * @return Response
	 */
	public function show($sid)
	{
		$image = $this->imageService->get($sid);

		// Can we edit this image? (@todo: temporary)
		$canEdit = false;
		$guestOwnsImage = in_array( $image->id, Session::get('owned_images', []) );

		if ($guestOwnsImage)
			$canEdit = true;

		return view('images/show')->with([
			'image'   => $image,
			'canEdit' => $canEdit
		]);
	}

	/**
	 * Process a download request for the specified image
	 *
	 * @param Request $request
	 * @param         $sidFile
	 *
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function download(Request $request, $sidFile)
	{
		// Split the filename and extension
		$sid = preg_replace('/\\.[^.\\s]{3,4}$/', '', $sidFile);
		$requestType = substr(strrchr($sidFile, "."), 1);

		// Retrieve the requested image
		$image = $this->imageService->get($sid);

		// What size image are we requesting?
		switch ( $request->get('size') ) {
			case 'preview':
				$scale = $image::PREVIEW;
				break;

			case 'thumbnail':
				$scale = $image::THUMBNAIL;
				break;

			default:
				$scale = $image::ORIGINAL;
		}

		// Make sure our requested type matches
		$imageType = $image->getType($scale);

		if ($imageType != $requestType) {
			return response()->redirectToRoute('images.download', [
				'size'    => $request->get('size'),
				'sidFile' => $sid.'.'.$imageType
			])->setStatusCode(301);
		}

        // Return the download response for our configured backend
		return $this->imageService->downloadResponse($image, $scale);
	}

	/**
	 * Remove the specified image
	 * DELETE /images/{sid}
	 *
	 * @param string $sid
	 * @param ImageDestroyRequest $request
	 *
	 * @return Response|App
	 */
	public function destroy(ImageDestroyRequest $request, $sid)
	{
		// Fetch our requested resource
		$image = $this->imageService->get($sid);

		// Delete an image using a key
		if ( $request->has('deleteKey') )
		{
			if ( $image->checkDeleteKey( $request->get('deleteKey') ) )
			{
				$this->imageService->delete($image);

				if ( $request->ajax() )
					return response()->json(['success' => true]);

				return response()->redirectToRoute('home');
			}
			else
			{
				if ( $request->ajax() )
					return response()->json('Invalid delete key provided', 403);

				return \App::abort(403);
			}
		}

		// Make sure we have permission to delete this image without a key
		if ( $image->canEdit() )
		{
			$this->imageService->delete($image);

			if ( $request->ajax() )
				return response()->json(['success' => true]);

			return response()->redirectToRoute('home');
		}

		return \App::abort(403);
	}

	/**
	 * Redirect short image URL's
	 *
	 * @param string $sid
	 *
	 * @return Response
	 */
	public function redirectShort($sid)
	{
		return response()->redirectToRoute('images.show', ['sid' => $sid], 301);
	}

    /**
     * Process a crop request for the specified image
     *
     * @param string           $sid
     * @param ImageCropRequest $request
     *
     * @return Response
     */
    public function crop($sid, ImageCropRequest $request)
    {
        // Fetch the requested image and drop it
        $image = $this->imageService->get($sid);
        $this->imageService->crop($image, $request->get('coords'));

        // Refresh the page (@todo Ajaxify)
        return response()->redirectToRoute('images.show', ['sid' => $sid]);
    }

}
