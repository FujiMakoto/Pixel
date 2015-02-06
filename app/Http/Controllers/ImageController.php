<?php namespace Pixel\Http\Controllers;

use Pixel\Contracts\Image\ImageContract;
use Pixel\Http\Requests;
use Pixel\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Pixel\Services\Image\ImageService;

class ImageController extends Controller {

	/**
	 * Display a listing of the resource.
	 * (This is just a placeholder, a public listing is still under consideration)
	 * GET /images
	 *
	 * @return Response
	 */
	public function index()
	{
		return response()->redirectToRoute('home');
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /images/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('images/create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /images
	 *
	 * @param ImageContract $image
	 * @param Request       $request
	 *
	 * @return Response
	 */
	public function store(ImageContract $image, Request $request)
	{
		// Create the image
		$image = $image->create( $request->file('image') );

		// Redirect to the newly created image resource
		if ( $request->ajax() )
			return response()->json(['redirect' => route('images.show', ['sid' => $image->sid])]);

		return response()->redirectToRoute('images.show', ['sid' => $image->sid]);
	}

	/**
	 * Display the specified resource.
	 * GET /images/{sid}
	 *
	 * @param ImageContract $imageService
	 * @param  string       $sid
	 *
	 * @return Response
	 */
	public function show(ImageContract $imageService, $sid)
	{
		$image = $imageService->get($sid);
		//dd($image);

		//return $imageService->downloadResponse($image, $image::PREVIEW);

		return view('images/show')->withImage($image);
	}

	public function download(ImageContract $imageService, Request $request, $sidFile)
	{
		// Split the filename and extension
		$sid  = preg_replace('/\\.[^.\\s]{3,4}$/', '', $sidFile);
		$requestType = substr(strrchr($sidFile, "."), 1);

		// Retrieve the requested image
		$image = $imageService->get($sid);

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

		// Make sure our requested type matches (@todo: this is very kludgy)
		$imagePath = $image->getRealPath($scale);
		$imageType = substr(strrchr($imagePath, "."), 1);

		if ($imageType != $requestType) {
			return response()->redirectToRoute('images.download', [
				'size'    => $request->get('size'),
				'sidFile' => $sid.'.'.$imageType
			])->setStatusCode(301);
		}

		return $imageService->downloadResponse($image, $scale);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /images/{sid}/edit
	 *
	 * @param  string  $sid
	 * @return Response
	 */
	public function edit($sid)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /images/{sid}
	 *
	 * @param  string  $sid
	 * @return Response
	 */
	public function update($sid)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /images/{sid}
	 *
	 * @param  string  $sid
	 * @return Response
	 */
	public function destroy($sid)
	{
		//
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
