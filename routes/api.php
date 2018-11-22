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
});
