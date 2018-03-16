<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('api')->group(function ()
{
	Route::prefix('pi')->group(function ()
	{
		Route::get('/detection', 'Pi\DetectionController@create');
	});

	Route::prefix('phone')->group(function ()
	{
		Route::post('/detection', 'Pi\DetectionController@create');
	});
});