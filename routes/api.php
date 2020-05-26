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

Route::get('user/generateJam', 'PolyController@generateJam');
Route::post('user/login', 'ApiController@login');
Route::post('user/create', 'ApiController@register');
Route::get('user/ruangan', 'BedMonitoringController@indexRuangan');
Route::post('user/detailRuangan', 'BedMonitoringController@index');

Route::get('user/poly', 'PolyController@indexPoly');
Route::post('user/polyDetail', 'PolyController@indexPolyDetail');
Route::post('user/polyDetailHari', 'PolyController@indexPolyDetailHari');

Route::get('user/shuttle', 'ShuttleBusController@indexShuttle');
Route::post('user/shuttleDetail', 'ShuttleBusController@indexShuttleDetail');

Route::get('user/faq', 'FaqController@index');

Route::post('user/berita', 'BeritaController@index');

Route::get('user/provinsi', 'WilayahController@indexProvinsi');
Route::post('user/kota', 'WilayahController@indexKota');
Route::post('user/kecamatan', 'WilayahController@indexKecamatan');
Route::post('user/desa', 'WilayahController@indexDesa');

Route::post('user/cekNomorKtpBpjs', 'ApiController@indexCekNomorKtpBpjs');

Route::get('user/suku', 'SukuController@index');

Route::get('user/agama', 'AgamaController@index');

Route::get('user/caraBayar', 'CaraBayarController@index');

Route::get('user/bahasa', 'BahasaController@index');

Route::get('user/negara', 'NegaraController@index');

Route::post('user/konfirmasiNomorMr', 'PasienController@konfirmasiNomorMr');
Route::post('user/tambahDataProfil', 'PasienController@tambahDataProfil');
Route::post('user/cekKtp', 'PasienController@cekKtp');

Route::get('user/rujukan', 'RujukanController@index');

Route::post('user/logout', 'ApiController@logout');


Route::group(['middleware' => 'jwt.verify'], function () {

    Route::post('user/getProfilDaftar', 'PendaftaranController@getProfilDaftar');
    Route::post('user/cekDaftar', 'PendaftaranController@indexStatus');
    Route::post('user/getUserLengkapiPendaftaran', 'PendaftaranController@getUserLengkapiPendaftaran');
    Route::post('user/getNomorMrSimpan', 'PendaftaranController@getNomorMrSimpan');
    Route::post('user/simpanNomr', 'PendaftaranController@simpanNomr');
    Route::post('user/cariNomorMr', 'PendaftaranController@cariNomorMr');
    Route::post('user/daftar', 'PendaftaranController@daftar');
    Route::post('user/getRiwayatPendaftaran', 'PendaftaranController@index');

    Route::post('user/updatePassword', 'PasienController@updatePassword');
    Route::post('user/updateProfilLengkapiPendaftaran', 'PasienController@updateProfilLengkapiPendaftaran');
    Route::post('user/updateProfil', 'PasienController@updateProfil');

    Route::post('user/updatePhoto', 'ApiController@updatePhoto');
    Route::get('user/user', 'ApiController@getAuthUser');

    Route::post('user/pengaduan', 'PengaduanController@index');
});
