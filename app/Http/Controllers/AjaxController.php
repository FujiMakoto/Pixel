<?php namespace Pixel\Http\Controllers;

use Illuminate\Http\Request;
use \Symfony\Component\HttpFoundation\Response;
use Pixel\Http\Requests\AjaxAccentuationRequest;
use Pixel\Repositories\Collection;;

class AjaxController extends Controller {

    /**
     * @param AjaxAccentuationRequest $request
     *
     * @return Response
     */
    public function accentuation(AjaxAccentuationRequest $request)
    {
        // Are we submitting a hex color code or RGB values?
        $color = $request->has('hex')
            ? \ColorScheme::hexToRgb( $request->get('hex') )
            : $request->only('red', 'green', 'blue');


        // Get the closest matching color scheme
        $response['colorScheme'] = \ColorScheme::getClosest($color);

        // Render the accentuation template
        $response['styling'] = view('images/_partials/accentuation', ['image' => new Collection($color)])->render();

        return response()->json($response);
	}

}
