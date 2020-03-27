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

Route::post('user/login', 'ApiController@login');
Route::post('user/create', 'ApiController@register');
Route::get('user/ruangan', 'BedMonitoringController@indexRuangan');
Route::post('user/detailRuangan', 'BedMonitoringController@index');
Route::get('user/poly', 'PolyController@indexPoly');
Route::post('user/polyDetail', 'PolyController@indexPolyDetail');
Route::get('user/shuttle', 'ShuttleBusController@indexShuttle');
Route::post('user/shuttleDetail', 'ShuttleBusController@indexShuttleDetail');
Route::get('user/faq', 'FaqController@index');
Route::post('user/berita', 'BeritaController@index');
Route::post('user/konfirmasiNomorMr', 'PasienController@konfirmasiNomorMr');

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::post('user/logout', 'ApiController@logout');

    Route::get('user/user', 'ApiController@getAuthUser');

    Route::get('products', 'ProductController@index');
    Route::get('products/{id}', 'ProductController@show');
    Route::post('products', 'ProductController@store');
    Route::put('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');
});
