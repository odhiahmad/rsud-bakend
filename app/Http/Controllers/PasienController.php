<?php


namespace App\Http\Controllers;


use App\Pasien;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasienController extends Controller
{
    public function konfirmasiNomorMr(Request $request)
    {

        if ($getPasien = Pasien::where('nomr', $request->nomorMr)->first()) {
            $tahunLahir = substr($getPasien->tgl_lahir, 0, 4);
            if ($tahunLahir == $request->tahunLahir) {
                if ($getPasien->id_user != 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor MR Ini sudah mendaftarkan akun, silahkan login menggunakan email yang sudah didaftarkan'
                    ], 200);

                } else {
                    return response()->json([
                        'success' => true,
                        'nama' => $getPasien->nama,
                        'nomorMr' => $getPasien->nomr,
                        'message' => 'Silahkan lanjut tahap berikut pendaftaran'
                    ], 200);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor Mr atau tahun lahir anda salah'
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nomor Mr atau tahun lahir anda salah'
            ], 200);
        }


    }

    public function konfirmasiNomorMrDua(Request $request)
    {
        $getPasien = Pasien::where('nomr', $request->nomorMr)->first();
        $tahunLahir = substr($getPasien->tgl_lahir, 0, 4);

        if ($tahunLahir == $request->tahunLahir) {
            if (User::where('email', $request->email)->count() == 0) {

                $user = new User();
                $user->nomr = $request->nomorMr;
                $user->name = $request->nama;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();

                $pasien = new Pasien();

                $data = [
                    'id_user' => $user->id,
                ];

                if ($pasien->where('nomr', $request->nomorMr)->update($data)) {
                    return response()->json([
                        'success' => true,
                        'id' => $user->id,
                        'name' => $user->name,
                        'message' => 'Selamat akun anda sudah terdaftar, silahkan login'
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Koneksi Bermasalah'
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nomor Mr atau tahun lahir anda salah'
            ], 200);
        }

    }
}
