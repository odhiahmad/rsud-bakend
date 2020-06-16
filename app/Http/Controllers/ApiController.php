<?php

namespace App\Http\Controllers;

use App\Dokter;
use App\DokterLibur;
use App\Http\Requests\RegisterAuthRequest;
use App\JadwalDokter;
use App\Notifikasi;
use App\Pasien;
use App\Pendaftaran;
use App\Poly;
use App\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Response;


use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    public $loginAfterSignUp = true;

    public function updatePhoto(Request $request)
    {
        $getUser = User::where('id', $request->id)->first();
        $getPasien = Pasien::where('id_user', $request->id)->first();

        $currentPhoto = $getUser->foto;

        if ($request->photo != $currentPhoto) {
            $name = $getPasien->no_ktp . '.jpg';

            Image::make($request->photo)->save(public_path('img/profile/') . $name);
            $request->merge(['photo' => $name]);

            $userPhoto = public_path('img/profile/') . $currentPhoto;
//            if (file_exists($userPhoto)) {
//                @unlink($userPhoto);
//            }
        }

        $data = [
            'foto' => $request->photo,
        ];

        $user = new Pasien();
        if ($user->where('id_user', $request->id)->update($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Foto anda berhasil di upload'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Foto anda gagal di upload'
            ], 200);
        }
    }

    public function liburDokter()
    {
        $date = Date('Y:m:d');
        $getUser = DokterLibur::where('libur_tgl', '=', '2019-08-31')->get();
        $dokterId = [];
        for ($i = 0; $i < count($getUser); $i++) {
            $dokterId[$i] = $getUser[$i]['libur_jadwalid'];
        }

        $getTrxJadwal = JadwalDokter::whereIn('jadwal_id', $dokterId)->get();

        $getDokterId = [];
        for ($i = 0; $i < count($getTrxJadwal); $i++) {
            $getDokterId[$i] = $getTrxJadwal[$i]['jadwal_dokter_id'];
        }

        $getDokter = Dokter::whereIn('dokter_id', $getDokterId)->get();

        return response()->json([
            'data' => $getDokter,
            'dataKeterangan' => $getUser,
        ], 200);

    }

    public function register(Request $request)
    {

        if (User::where('email', $request->email)->count() == 0) {


            $user = new User();
            $user->nomr = $request->nomorMr;
            $user->name = $request->nama;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);


            if ($user->save()) {
                if ($request->nomorMr != null) {
                    $pasien = new Pasien();
                    $tes = Pasien::where('nomr', $request->nomorMr)->first();

                    $data = [
                        'id_user' => $user->id,
                        'nama' => $user->name
                    ];

                    if ($pasien->where('nomr', $request->nomorMr)->update($data)) {
                        return response()->json([
                            'success' => true,
                            'id' => $user->id,
                            'name' => $user->name,
                            'message' => 'Selamat akun anda sudah terdaftar, silahkan login'
                        ], 200);
                    }

                } else {
                    $pasien = new Pasien();
                    $pasien->id_user = $user->id;
                    $pasien->nama = $user->name;
                    $pasien->save();

                    return response()->json([
                        'success' => true,
                        'id' => $user->id,
                        'name' => $user->name,
                        'message' => 'Selamat akun anda sudah terdaftar, silahkan login'
                    ], 200);
                }


            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi Jaringan'
                ], 200);
            }

//                Mail::send('mail', ['nama' => $request->email, 'pesan' => 'admin123'], function ($message) use ($request)
//                {
//                    $message->from('rsud.pp@padangpanjang.go.id', 'RSUD PADANG PANJANG');
//                    $message->to($request->email);
//                });

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email Sudah terdaftar'
            ], 200);
        }

    }

    public function indexCekNomorKtpBpjs(Request $request)
    {
        if (strlen($request->nomor) == 16) {
            $getPasien = Pasien::where('no_ktp', $request->nomor)->count();
            if ($getPasien == 0) {
                return response()->json([
                    'jenis' => 'nik',
                    'status' => true,
                ], 401);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Nomor NIK Sudah terdaftar di aplikasi',
                ], 401);
            }
        } else if (strlen($request->nomor) == 13) {
            $getPasien = Pasien::where('no_bpjs', $request->nomor)->count();
            if ($getPasien == 0) {
                return response()->json([
                    'jenis' => 'nokartu',
                    'status' => true,
                ], 401);
            } else {
                return response()->json([
                    'jenis' => 'nobpjs',
                    'status' => false,
                    'message' => 'Nomor BPJS Sudah terdaftar di aplikasi',
                ], 401);
            }

        }

    }

    public function login(Request $request)

    {
        $input = $request->only('email', 'password');
        $jwt_token = null;


        JWTAuth::factory()->setTTL(999999999999999999);
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        } else {
            $getUser = User::where('email', $request->email)->first();
            $getPasien = Pasien::where('id_user', $getUser->id)->first();

            if ($getUser->login == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ini telah login pada perangkat lain',
                ], 401);
            } else {
                if ($getPasien->no_ktp == 0) {
                    return response()->json([
                        'success' => true,
                        'status' => true,
                        'id' => $getUser->id,
                        'token' => $jwt_token,
                        'expires_in' => JWTAuth::factory()->getTTL() * 60
                    ]);
                } else {

                    return response()->json([
                        'success' => true,
                        'status' => false,
                        'id' => $getUser->id,
                        'token' => $jwt_token,
                        'expires_in' => JWTAuth::factory()->getTTL() * 60
                    ]);

                }


            }

        }

    }


    public function logout(Request $request)
    {

        $getUser = User::where('id', $request->id)->first();

        $user = new User();

        $data = [
            'login' => 0,
        ];

        if ($user->where('id', $getUser->id)->update($data)) {

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);

        } else {
            return response()->json([
                'message' => 'Gangguan Jaringan',
                'success' => false,
            ], 401);
        }


    }

    public function updateStatusLogin(Request $request)
    {
        $user = new User();
        $data = [
            'login' => 1,
        ];

        if ($user->where('id', $request->id)->update($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil'
            ]);
        } else {
            return response()->json([
                'message' => 'Gangguan Jaringan',
                'success' => false,
            ], 401);
        }
    }

    public function getAuthUser(Request $request)
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $getUser = compact('user');


        $getPasien = Pasien::where('id_user', $getUser['user']['id'])->first();
        $path = public_path() . '/img/profile/' . $getUser['user']['foto'];
        return response()->json([
            'dataUser' => compact('user'),
            'dataProfile' => $getPasien,
            'image' => asset('img/profile/' . $getPasien['foto']),
        ], 401);


    }

    public function getNotifikasi(Request $request)
    {
        $notifikasi = Notifikasi::where(['id_user' => $request->id])->orderBy('id', 'DESC')->get();

        return response()->json([
            'data' => $notifikasi,
            'status' => 'ok'
        ]);
    }

    public function getDataDashboard(Request $request)
    {
        $ket1 = [];
        $ket1[0] = 'Jumlah Mendaftar';
        $ket1[1] = 'Jumlah Mendaftarkan Diri Sendiri';
        $ket1[2] = 'Jumlah Mendaftarkan Orang Lain';

        $getDataAkun = Pasien::where(['id_user' => $request->id])->first();
        $jumlahMendaftar = Pendaftaran::where(['idUserDaftar' => $request->id])->count();
        $jumlahMendaftarDiriSendiri = Pendaftaran::where(['idUserDaftar' => $request->id, 'no_ktp' => $getDataAkun->no_ktp])->count();
        $jumlahMendaftarOrangLain = Pendaftaran::where('idUserDaftar', $request->id)->where('no_ktp', '!=', $getDataAkun->no_ktp)->count();

        $jumlah = [];
        $jumlah[0] = $jumlahMendaftar;
        $jumlah[1] = $jumlahMendaftarDiriSendiri;
        $jumlah[2] = $jumlahMendaftarOrangLain;

        $data = [];
        for ($i = 0; $i < count($jumlah); $i++) {
            $data[$i] = [
                'Keterangan' => $ket1[$i],
                'Jumlah' => $jumlah[$i]
            ];
        }


        return response()->json([
            'data' => $data,
            'status' => 'ok'
        ]);
    }
}
