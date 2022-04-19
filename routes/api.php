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

//TEST
Route::get('test', function () {
    return 'Hello';
});

Route::group(["prefix" => "v1"], function () {


    // authentication
    Route::group(['prefix' => 'auth'], function () {

        Route::post('signup', 'v1\Auth\RegisterController@register');
        Route::post('login', 'v1\Auth\LoginController@login');
        Route::get('logout', 'v1\Auth\LoginController@logout')->middleware("auth:api");
    });

    //Authenticated Routes
    Route::group(['middleware' => 'auth:api'], function () {

        //Admin
        Route::group(['prefix' => 'admin'], function () {

            Route::get('fetch/all/admin',                                           'v1\Admin\AdminController@listAllAdmins');
            Route::get('fetch/a/admin/{id}',                                        'v1\Admin\AdminController@show');
            Route::post('create/single/admin',                                      'v1\Admin\AdminController@store');
            Route::post('update/a/admin/{id}',                                      'v1\Admin\AdminController@update');
            Route::delete('delete/a/admin/{id}',                                    'v1\Admin\AdminController@destroy');
            Route::post('assign/an/employee/{id}',                                  'v1\Admin\AdminController@assignEmployee');

        });

        //Employee
        Route::group(['prefix' => 'employee'], function () {

            Route::get('fetch/all/employee',                                        'v1\Employee\EmployeeController@listAllEmployees');
            Route::get('fetch/a/employee/{id}',                                     'v1\Employee\EmployeeController@show');
            Route::post('create/single/employee',                                   'v1\Employee\EmployeeController@store');
            Route::post('update/a/employee/{id}',                                   'v1\Employee\EmployeeController@update');
            Route::delete('delete/a/employee/{id}',                                 'v1\Employee\EmployeeController@destroy');

        });

        //Review
        Route::group(['prefix' => 'review'], function () {

            Route::get('fetch/all/review',                                          'v1\Review\ReviewController@listAllReviews');
            Route::get('fetch/a/review/{id}',                                       'v1\Review\ReviewController@show');
            Route::post('create/single/review',                                     'v1\Review\ReviewController@store');
            Route::post('update/a/review/{id}',                                     'v1\Review\ReviewController@update');
            Route::delete('delete/a/review/{id}',                                   'v1\Review\ReviewController@destroy');
            Route::get('fetch/reviews/for/feedback',                                'v1\Review\ReviewController@listReviewsForFeedback');
            Route::post('feedback/for/a/review/{id}',                               'v1\Review\ReviewController@reviewFeedback');

        });

    });

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
