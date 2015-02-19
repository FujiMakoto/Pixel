<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', [
	'as'   => 'home',
	'uses' => 'ImageController@create'
]);

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

// Image Resources
Route::group(['prefix' => 'images'], function ()
{
	Route::pattern('sid', '[0-9A-Za-z]{7}');

	Route::get('/', [
		'as'   => 'images.index',
		'uses' => 'ImageController@index'
	]);

	Route::get('create', [
		'as'   => 'images.create',
		'uses' => 'ImageController@create'
	]);
	Route::post('/', [
		'as'   => 'images.store',
		'uses' => 'ImageController@store'
	]);

	Route::get('{sid}', [
		'as'    => 'images.show',
		'uses'  => 'ImageController@show'
	]);
	Route::get('{sidFile}', [
		'as'   => 'images.download',
		'uses' => 'ImageController@download'
	])
	->where(['sidFile' => '[0-9A-Za-z]{7}\\.[a-z]{3,4}']);

	Route::get('{sid}/edit', [
		'as'   => 'images.edit',
		'uses' => 'ImageController@edit'
	]);
	Route::put('{sid}', [
		'as'   => 'images.update',
		'uses' => 'ImageController@update'
	]);

	Route::delete('{sid}', [
		'as'   => 'images.destroy',
		'uses' => 'ImageController@destroy'
	]);
});

// Short URL's
Route::group(['prefix' => 'i'], function ()
{
	Route::get('{sid}', [
		'as'   => 'images.shortShow',
		'uses' => 'ImageController@redirectShort'
	]);

	Route::get('{sidFile}', [
		'as'   => 'images.shortDownload',
		'uses' => 'ImageController@download'
	])
	->where(['sidFile' => '[0-9A-Za-z]{7}\\.[a-z]{3,4}']);
});

// User routes
Route::group(['prefix' => 'register'], function()
{
    // Registration
    Route::get('', [
        'as'   => 'users.auth.register',
        'uses' => 'UserAuthController@register'
    ]);
    Route::post('', [
        'as'   => 'users.auth.doRegister',
        'uses' => 'UserAuthController@doRegister'
    ]);

    // Activation
    Route::get('activation', [
        'as'   => 'users.auth.activate',
        'uses' => 'UserAuthController@activate'
    ]);
    Route::match(['GET', 'POST'], 'activate', [
        'as'   => 'users.auth.doActivate',
        'uses' => 'UserAuthController@doActivate'
    ]);

    // Cancellation
    Route::match(['GET', 'POST'], 'cancel', [
        'as'   => 'users.auth.abortRegister',
        'uses' => 'UserAuthController@abortRegister'
    ]);
});

Route::group(['prefix' => 'login'], function ()
{
    Route::get('', [
        'as'   => 'users.auth.login',
        'uses' => 'UserAuthController@login'
    ]);
    Route::post('', [
        'as'   => 'users.auth.doLogin',
        'uses' => 'UserAuthController@doLogin'
    ]);
});

Route::get('logout', [
    'as'   => 'users.auth.logout',
    'uses' => 'UserAuthController@logout'
]);

// Ajax routes
Route::group(['prefix' => 'ajax'], function()
{
	Route::match(['GET', 'POST'], 'accentuation', [
		'as'   => 'ajax.accentuation',
		'uses' => 'AjaxController@accentuation'
	]);
});
