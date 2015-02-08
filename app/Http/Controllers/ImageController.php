<?php namespace Pixel\Http\Controllers;

use Pixel\Http\Requests;
use Pixel\Http\Requests\ImageUploadRequest;
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
		$image = $this->imageService->create( $request->file('image') );

		// If we're a guest, set an ownership flag in our session
		if ( \Auth::guest() )
			Session::push('owned_images', $image->id);

		// Redirect to the newly created image resource
		if ( $request->ajax() )
			return response()->json(['redirect' => route('images.show', ['sid' => $image->sid])]);

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
	 * Display the confirmation page for image deletion
	 * GET /images/{sid}/delete/{deleteKey}
	 *
	 * @param $sid
	 * @param $deleteKey
	 *
	 * @return Response
	 */
	public function delete($sid, $deleteKey)
	{
		// Set the deleteKey and return the default show method
		view()->share('deleteKey', $deleteKey);
		return $this->show($sid);
	}

	/**
	 * Remove the specified image
	 * DELETE /images/{sid}
	 *
	 * @param string $sid
	 * @param Request $request
	 *
	 * @return Response|App
	 */
	public function destroy($sid, Request $request)
	{
		// Fetch our requested resource
		$image = $this->imageService->get($sid);

		// Was this image uploaded by a guest?
		if ($image->user_id == 0) {
			// Did we receive a valid delete key with our request?
			if ( $request->has('deleteKey') && ($request->get('deleteKey') == $image->delete_key) ) {
				// Delete the image and return success
				$this->imageService->delete($image);

				if ( $request->ajax() )
					return response()->json(['success' => true]);

				return response()->redirectToRoute('home');
			} else {
				return \App::abort(403);
			}
		}
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

}
