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

Route::get('/pegawai', 'PegawaiController@index');
Route::get('/pegawai/cetak_pdf', 'PendaftaranController@CetakLaporan');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
