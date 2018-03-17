<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('pi')->group(function ()
{
	Route::get('/detection', 'Pi\DetectionController@create');
});

Route::prefix('phone')->group(function ()
{
	Route::get('/registration', 'Phone\AuthController@registration');
    Route::post('/image', 'Phone\ImageController@upload');
});
