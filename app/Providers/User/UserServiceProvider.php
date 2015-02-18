<?php namespace Pixel\Providers\User;

use Pixel\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->extend('pixel',function()
        {
            return app('Pixel\Contracts\User\UserContract');
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Pixel\Contracts\User\UserContract',
            'Pixel\Services\User\UserService'
        );
    }

}