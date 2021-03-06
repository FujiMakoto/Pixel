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
    Route::post('{sid}/crop', [
        'as'   => 'images.crop',
        'uses' => 'ImageController@crop'
    ]);

	Route::delete('{sid}', [
		'as'   => 'images.destroy',
		'uses' => 'ImageController@destroy'
	]);
});

// Album Resources
Route::group(['prefix' => 'albums'], function()
{
    Route::pattern('sid', '[0-9A-Za-z]{7}');

    Route::get('/', [
        'as'   => 'albums.index',
        'uses' => 'AlbumController@index'
    ]);

    Route::get('create', [
        'as'   => 'albums.create',
        'uses' => 'AlbumController@create'
    ]);
    Route::post('/', [
        'as'   => 'albums.store',
        'uses' => 'AlbumController@store'
    ]);

    Route::get('{sid}/upload', [
        'as'   => 'albums.upload',
        'uses' => 'AlbumController@upload'
    ]);

    Route::get('{sid}', [
        'as'   => 'albums.show',
        'uses' => 'AlbumController@show'
    ]);

    /*Route::get('{sid}/edit', [
        'as'   => 'albums.edit',
        'uses' => 'AlbumController@edit'
    ]);
    Route::put('{sid}', [
        'as'   => 'albums.update',
        'uses' => 'AlbumController@update'
    ]);*/

    Route::delete('{sid}', [
        'as'   => 'albums.destroy',
        'uses' => 'AlbumController@destroy'
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

// User Registration routes
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

// User Authentication routes
Route::group(['prefix' => 'login'], function ()
{
    // Login
    Route::get('', [
        'as'   => 'users.auth.login',
        'uses' => 'UserAuthController@login'
    ]);
    Route::post('', [
        'as'   => 'users.auth.doLogin',
        'uses' => 'UserAuthController@doLogin'
    ]);

    // OAuth login
    Route::get('oauth/{driver}', [
        'as'   => 'users.auth.oauth',
        'uses' => 'UserAuthController@oauth'
    ]);

    // Recover password
    Route::get('recover', [
        'as'   => 'users.auth.recover',
        'uses' => 'UserAuthController@recover'
    ]);
    Route::post('recover', [
        'as'   => 'users.auth.doRecover',
        'uses' => 'UserAuthController@doRecover'
    ]);

    // Reset password
    Route::get('reset/{token}', [
        'as'   => 'users.auth.reset',
        'uses' => 'UserAuthController@reset'
    ]);
    Route::post('reset', [
        'as'   => 'users.auth.doReset',
        'uses' => 'UserAuthController@doReset'
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
