<?php

class ImageControllerTest extends TestCase {

    /**
     * @var Mockery\Mock
     */
    private $imageService;

    /**
     * Sample data
     *
     * @var array
     */
    protected $data;

    public function __construct()
    {
        $this->data = [
            'attributes' => [
                'id'              => 3,
                'sid'             => 'aBcDeF7',
                'md5sum'          => 'abbfc75e023cf46439383c18ac4b4f11',
                'delete_key'      => 'jCYz1uzEuzRVuvKA8Pgj0IIKhai0jfz8yRzRg5qt',
                'album_id'        => 0,
                'name'            => 'sample_image.jpg',
                'type'            => 'jpg',
                'size'            => 842600,
                'views'           => 27,
                'width'           => 1920,
                'height'          => 1080,
                'original_width'  => 1920,
                'original_height' => 1080,
                'user_id'         => 0,
                'upload_ip'       => '127.0.0.1',
                'upload_uagent'   => 'Mozilla/5.0 Sample User Agent',
                'red'             => 73,
                'green'           => 66,
                'blue'            => 83,
                'created_at'      => '2015-02-08 07:06:15',
                'updated_at'      => '2015-02-08 07:06:15',
                'expires'         => null
            ]
        ];
    }

    public function setUp()
    {
        parent::setUp();
        Session::start();

        $this->imageService = Mockery::mock('Pixel\Contracts\Image\ImageContract');
        $this->app->instance('Pixel\Contracts\Image\ImageContract', $this->imageService);
    }


    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }


    public function test_displays_image_directory()
    {
        $response = $this->route('GET', 'images.index');

        $this->assertRedirectedToRoute('home');
    }


    public function test_displays_image_upload_form()
    {
        $response = $this->route('GET', 'images.create');

        $this->assertResponseOk();
    }


    public function test_displays_a_valid_image_resource()
    {
        // Route params
        $sid = $this->data['attributes']['sid'];

        // Create a new repository instance
        $repository = $this->app->make('Pixel\Contracts\Image\RepositoryContract', $this->data);

        // Mock our image service request
        $this->imageService->shouldReceive('get')->once()->with($sid)->andReturn($repository);

        // Perform a GET request to the image preview page
        $response = $this->route('GET', 'images.show', ['sid' => $sid]);
        $this->assertResponseOk();
        $this->assertViewHas('image');

        // Make sure the view has a valid instance of our repository object
        $image = $response->original->getData()['image'];
        $this->assertInstanceOf('Pixel\Contracts\Image\RepositoryContract', $image);
    }


    public function test_deletes_an_image_using_guest_delete_key()
    {
        // Route params
        $params['sid']       = $this->data['attributes']['sid'];
        $params['deleteKey'] = $this->data['attributes']['delete_key'];
        $params['_token']    = Session::token();

        // Create a new repository instance
        $repository = $this->app->make('Pixel\Contracts\Image\RepositoryContract', $this->data);

        // Mock our image service request
        $this->imageService->shouldReceive('get')->once()->with($params['sid'])->andReturn($repository);
        $this->imageService->shouldReceive('delete')->once()->with($repository)->andReturn(true);

        // Submit the DELETE request
        $response = $this->route('DELETE', 'images.destroy', $params);

        $this->assertRedirectedToRoute('home');
    }


    public function test_delete_fails_if_invalid_key_provided()
    {
        // Route params
        $params['sid']       = $this->data['attributes']['sid'];
        $params['deleteKey'] = str_shuffle($this->data['attributes']['delete_key']);
        $params['_token']    = Session::token();

        // Create a new repository instance
        $repository = $this->app->make('Pixel\Contracts\Image\RepositoryContract', $this->data);

        // Mock our image service request
        $this->imageService->shouldReceive('get')->once()->with($params['sid'])->andReturn($repository);
        $this->imageService->shouldNotHaveReceived('delete');

        // Submit the DELETE request
        $response = $this->route('DELETE', 'images.destroy', $params);

        $this->assertResponseStatus(403);
    }


    public function test_invalid_image_sid_should_fail()
    {
        // Invalid characters in string identifier
        $sid = 'aB_dEf7';

        $response = $this->route('GET', 'images.show', ['sid' => $sid]);

        $this->assertResponseStatus(404);

        // Invalid length for string identifier
        $sid = 'aBcDEfG8';

        $response = $this->route('GET', 'images.show', ['sid' => $sid]);

        $this->assertResponseStatus(404);
    }


}
