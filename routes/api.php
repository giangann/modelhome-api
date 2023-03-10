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
    Route::post('home-page/update','HomePageController@handleUpdate');
    Route::apiResource('home-page','HomePageController');

    Route::post('posts/create','PostController@store');
    Route::apiResource('projects','ProjectController');
    Route::apiResource('posts','PostController');

    Route::group(['middleware'=>'auth:api'],function (){
        Route::apiResources([
        ]);
    });
});
