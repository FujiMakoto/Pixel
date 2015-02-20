<?php namespace Pixel\Http\Controllers;

use Pixel\Contracts\Album\AlbumContract;
use Pixel\Http\Requests\AlbumCreationRequest;
use \Illuminate\View\View;
use \Symfony\Component\HttpFoundation\Response;

class AlbumController extends Controller {

    /**
     * @var AlbumContract
     */
    protected $albumService;

    /**
     * Constructor
     *
     * @param AlbumContract $albumService
     */
	public function __construct(AlbumContract $albumService)
    {
        $this->albumService = $albumService;
    }

    /**
     * Redirect to the homepage
     * GET /albums
     *
     * @return Response
     */
    public function index()
    {
        return response()->redirectToRoute('home');
    }

    /**
     * Show the album creation form
     * GET /albums/create
     *
     * @return View
     */
    public function create()
    {
        return view('albums/create');
    }

    /**
     * Process the album creation request
     *
     * @param AlbumCreationRequest $request
     *
     * @return Response
     */
    public function store(AlbumCreationRequest $request)
    {
        $album = $this->albumService->create( $request->only('name', 'description') );

        return response()->redirectToRoute('albums.upload', ['sid' => $album->sid]);
    }

    /**
     * Display the image upload form for the specified album
     *
     * @param string $sid
     *
     * @return View|Response
     */
    public function upload($sid)
    {
        $album = $this->albumService->get($sid);

        // Make sure we have upload access to this album
        if ( ! $album->canEdit() )
            return abort(403);

        return view('albums/upload')->with(['album' => $album]);
    }

    /**
     * Display the specified album
     *
     * @param string $sid
     *
     * @return View
     */
    public function show($sid)
    {
        $album = $this->albumService->get($sid);

        return view('albums/show')->with(['album' => $album]);
    }

}
