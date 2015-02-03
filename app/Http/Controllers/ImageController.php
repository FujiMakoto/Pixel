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
		return \Redirect::route('home');
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
	 * @param ImageContract $image
	 * @param  string       $sid
	 *
	 * @return Response
	 */
	public function show(ImageContract $image, $sid)
	{
		$image = $image->get($sid);
		//dd($image);

		return view('images/show')->withImage($image);
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

}
