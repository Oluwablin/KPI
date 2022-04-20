<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

//TEST
Route::get('test', function () {
    return 'Hello';
});

Route::group(["prefix" => "v1"], function () {


    // authentication
    Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers'], function () {

        Route::post('signup', 'v1\Auth\RegisterController@register');
        Route::post('login', 'v1\Auth\LoginController@login');
        Route::get('logout', 'v1\Auth\LoginController@logout')->middleware("auth:api");
    });

    //Authenticated Routes
    Route::group(['middleware' => 'auth:api', 'namespace' => 'App\Http\Controllers'], function () {

        //Admin
        Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function () {

            Route::get('fetch/all/admins',                                          'v1\Admin\AdminController@listAllAdmins');
            Route::get('fetch/all/employees',                                       'v1\Admin\AdminController@listAllEmployees');
            Route::get('fetch/an/employee/{id}',                                    'v1\Admin\AdminController@show');
            Route::post('create/single/employee',                                   'v1\Admin\AdminController@store');
            Route::put('update/an/employee',                                        'v1\Admin\AdminController@update');
            Route::delete('remove/an/employee',                                     'v1\Admin\AdminController@destroy');
            Route::post('assign/an/employee/{id}',                                  'v1\Admin\AdminController@assignEmployee');

            //Performance Review
            Route::get('fetch/all/reviews',                                         'v1\Review\ReviewController@listAllReviews');
            Route::get('fetch/a/review/{id}',                                       'v1\Review\ReviewController@show');
            Route::post('create/single/review',                                     'v1\Review\ReviewController@store');
            Route::post('update/a/review/{id}',                                     'v1\Review\ReviewController@update');

        });

        //Employee
        Route::group(['prefix' => 'employee'], function () {

            Route::get('fetch/reviews/for/feedback',                                'v1\Employee\EmployeeController@listReviewsForFeedback');
            Route::post('feedback/for/a/review/{id}',                               'v1\Employee\EmployeeController@reviewFeedback');

        });

        //Review
        // Route::group(['prefix' => 'review'], function () {

        //     Route::get('fetch/all/reviews',                                         'v1\Review\ReviewController@listAllReviews');
        //     Route::get('fetch/a/review/{id}',                                       'v1\Review\ReviewController@show');
        //     Route::post('create/single/review',                                     'v1\Review\ReviewController@store');
        //     Route::post('update/a/review/{id}',                                     'v1\Review\ReviewController@update');
        //     Route::delete('delete/a/review/{id}',                                   'v1\Review\ReviewController@destroy');
        //     Route::get('fetch/reviews/for/feedback',                                'v1\Review\ReviewController@listReviewsForFeedback');
        //     Route::post('feedback/for/a/review/{id}',                               'v1\Review\ReviewController@reviewFeedback');

        // });

    });

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
