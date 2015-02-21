<?php namespace Pixel\Providers\Image;

use Illuminate\Support\ServiceProvider;
use Pixel\Providers\Image\BackendServiceProvider as ImageRepository;
use Pixel\Services\Image;

class ImageServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Pixel\Contracts\Image\ImageContract',
            'Pixel\Services\Image\ImagickDriver'
        );

//        $this->app->singleton('image', function($app)
//        {
//            return new ImageContract($app->make('Pixel\Contracts\Image\Repository'));
//        });
    }

}
