<?php namespace Pixel\Providers;

use Illuminate\Support\ServiceProvider;
use Pixel\Services\Image\ImageValidator;

class ValidatorServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any necessary services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->validator->resolver(function($translator, $data, $rules, $messages = [], $customAttributes = [])
        {
            return new ImageValidator($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}