<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => '\App\Http\Controllers\Api'], function () {
    Route::get('tags/by-tag-id/{id}','TagController@byTagId');
    Route::get('tags/by-project/{id}','TagController@byProject');
    Route::get('tags/by-blog','TagController@byBlog');
    Route::post('home-page/update','HomePageController@handleUpdate');
    Route::apiResource('home-page','HomePageController');

    Route::post('posts/create','PostController@store');
    Route::get('projects/get-by-slug/{slug}','ProjectController@getBySlug');
    Route::post('projects/update/{id}','ProjectController@update');
    Route::post('blogs/update/{id}','BlogController@update');
    Route::get('blogs/get-by-slug/{slug}','BlogController@getBySlug');

    Route::apiResource('projects','ProjectController');
    Route::apiResource('blogs','BlogController');
    Route::apiResource('posts','PostController');
    Route::apiResource('tags','TagController');



    Route::group(['middleware'=>'auth:api'],function (){
        Route::apiResources([
        ]);
    });
});
