<?php

use Illuminate\Support\Facades\Route;

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

Route::post('/register', "Api\UserController@register");
Route::post('/login', "Api\UserController@login");

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/user', "Api\UserController@details");

    Route::post('/languages', "Api\LanguageController@create");
    Route::delete('/languages/{code}', "Api\LanguageController@delete");
    Route::get('/languages', "Api\LanguageController@getAll");

    Route::post('/brands', "Api\BrandController@create");
    Route::delete('/brands/{id}', "Api\BrandController@delete");
    Route::post('/brands/{id}', "Api\BrandController@update");
    Route::get('/brands', "Api\BrandController@getAll");
    Route::get('/brands/{id}', "Api\BrandController@get");

    Route::post('/seasons', "Api\SeasonController@create");
    Route::delete('/seasons/{id}', "Api\SeasonController@delete");
    Route::post('/seasons/{id}', "Api\SeasonController@update");
    Route::get('/seasons', "Api\SeasonController@getAll");
    Route::get('/seasons/{id}', "Api\SeasonController@get");

    Route::post('/collections', "Api\CollectionController@create");
    Route::delete('/collections/{id}', "Api\CollectionController@delete");
    Route::post('/collections/{id}', "Api\CollectionController@update");
    Route::get('/collections', "Api\CollectionController@getAll");
    Route::get('/collections/{id}', "Api\CollectionController@get");

    Route::post('/categories', "Api\CategoryController@create");
    Route::delete('/categories/{id}', "Api\CategoryController@delete");
    Route::post('/categories/{id}', "Api\CategoryController@update");
});
