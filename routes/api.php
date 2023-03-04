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
Route::group(['namespace' => '\App\Http\Controllers\Api','middleware'=>'language'], function () {
    Route::post('login-with-google', [GoogleController::class, 'loginWithGoogle']);

    Route::get('post/get-by-coin-id/{coin_id}','PostController@getByCoinId');

    Route::get('comment/get-by-post-id/{coin_id}','CommentController@getByPostId');

    Route::get('like/count-like-of-post/{post_id}', 'LikeController@countLikeOfPost');

    Route::post('coin/store-array', 'CoinController@storeArray');
    Route::post('coin/crawl-uuid', 'CoinController@crawlUuid');
    Route::get('coin/search','CoinController@searchByCoinName');

    Route::get('coin/all', 'CoinController@getAll');
    Route::get('coin/{id}', 'CoinController@show');

    Route::get('get-setting', 'SettingController@getSetting');

    Route::get('get-admin-contact', 'UserController@getAdminInformation');

    Route::group(['middleware'=>'auth:api'],function (){
        Route::get('me', 'UserController@me');
        Route::get('user/calculate-score', 'UserController@calculateScore');
        Route::get('user/calculate-money', 'UserController@calculateMoneyByUserId');

        Route::post('user/update-role/{id}','UserController@updateUserRole');

        Route::post('like/like', 'LikeController@like');
        Route::get('watch-list/{id}', 'WatchListController@watchListByUserId');
        Route::delete('watch-list/{coin_id}/{user_id}','WatchListController@removeCoinOfWatchList');

        Route::post('donate/donate', 'DonateController@donateByUserId');
        Route::get('donate/get-by-my-id','DonateController@byUserId');

        Route::get('form/get-by-user','DonateController@getByUser');
//        Route::post('score-to-money-form',)

        Route::get('notification/by-user','NotificationController@getNotiByUserWithSenderInfo');
        Route::get('notification/new-count','NotificationController@newCount');
        Route::post('notification/mark-as-read/{id}','NotificationController@markAsRead');
        Route::post('notification/mark-all-as-read','NotificationController@markAllAsRead');
        Route::post('notification/mark-as-seen','NotificationController@markAsSeen');

        Route::get('money-statistic','DashboardController@moneyStatistic');
        Route::apiResources([
            'coin' => 'CoinController',
            'comment' => 'CommentController',
            'like' => 'LikeController',
            'post' => 'PostController',
            'reply' => 'ReplyController',
            'watch-list'=>'WatchListController',
            'donate'=>'DonateController',
            'score-to-money-form' =>'ScoreToMoneyFormController',
            'notification' => 'NotificationController',
            'setting'=>'SettingController',
            'user'=>'UserController'
        ]);

    });
});
