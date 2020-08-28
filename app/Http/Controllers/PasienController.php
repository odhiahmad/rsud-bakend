<?php


namespace App\Http\Controllers;


use App\Pasien;
use App\Pendaftaran;
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

    public function cekKtp(Request $request)
    {
        $cekKtp = Pasien::where('no_ktp', $request->nik)->count();

        if ($cekKtp === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'KTP anda sudah terdaftar pada akun lain'
            ], 200);
        }
    }

    public function tambahDataProfil(Request $request)
    {
        $getPasien = Pasien::where('id_user', $request->id)->first();
        $tahunLahir = substr($getPasien->tgl_lahir, 0, 4);


        $pasien = new Pasien();
        $jenisKelamin = '';
        if ($request->jenisKelamin === 'L') {
            $jenisKelamin = 1;
        } else {
            $jenisKelamin = 2;
        }
        $data = [
            'no_ktp' => $request->nik,
            'nama' => $request->nama,
            'tgl_lahir' => $request->tanggalLahir,
            'jns_kelamin' => $jenisKelamin,
            'no_bpjs' => $request->noKartu,
            'no_telpon' => $request->no_telpon,
        ];

        if ($pasien->where('id_user', $request->id)->update($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Selamat bpjs anda sudah didaftarkan ke dalam sistem'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi Bermasalah'
            ], 200);
        }


    }

    public function updateProfil(Request $request)
    {
        $getPasien = Pasien::where('id_user', $request->id)->first();
        $tahunLahir = substr($request->tanggalLahir, 0, 10);

        $pasien = new Pasien();

        $data = [
            'jns_kelamin' => $request->jenisKelamin,
            'status_kawin' => $request->statusKawin,
            'tgl_lahir' => $tahunLahir,
//            'no_telpon' => $request->noTelpon,
            'tempat_lahir' => $request->tempatLahir,
            'nama' => $request->nama,
            'pekerjaan' => $request->pekerjaan,
            'no_ktp' => $request->nik,
            'nama_provinsi' => $request->pilihProvinsi,
            'nama_kab_kota' => $request->pilihKota,
            'nama_kecamatan' => $request->pilihKecamatan,
            'nama_kelurahan' => $request->pilihDesa,
            'suku' => $request->pilihSuku,
            'bahasa' => $request->pilihBahasa,
            'nama_negara' => $request->pilihNegara,
            'agama' => $request->agama,
            'kewarganegaraan' => $request->pilihWn,
            'alamat' => $request->alamat,
            'penanggung_jawab' => $request->penanggungJawab,
            'no_penanggung_jawab' => $request->noHpPenanggungJawab,
            'no_bpjs' => $request->noBpjs,
        ];

        if ($pasien->where('id_user', $request->id)->update($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Selamat data profil anda sudah diinputkan'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi Bermasalah'
            ], 200);
        }
    }

    public function updateProfilLengkapiPendaftaran(Request $request)
    {
        $getPasien = Pasien::where('id_user', $request->id)->first();
        $tahunLahir = substr($request->tanggalLahir, 0, 10);

        $pasien = new Pasien();
        $user = new User();

        $data1 = [
            'status' => 0
        ];

        $data = [
            'jns_kelamin' => $request->jenisKelamin,
            'status_kawin' => $request->statusKawin,
            'tgl_lahir' => $tahunLahir,
            'tempat_lahir' => $request->tempatLahir,
            'pekerjaan' => $request->pekerjaan,
            'no_ktp' => $request->nik,
            'nama_provinsi' => $request->pilihProvinsi,
            'nama_kab_kota' => $request->pilihKota,
            'nama_kecamatan' => $request->pilihKecamatan,
            'nama_kelurahan' => $request->pilihDesa,
            'suku' => $request->pilihSuku,
            'bahasa' => $request->pilihBahasa,
            'nama_negara' => $request->pilihNegara,
            'agama' => $request->agama,
            'kewarganegaraan' => $request->pilihWn,
            'alamat' => $request->alamat,
            'penanggung_jawab' => $request->penanggungJawab,
            'no_penanggung_jawab' => $request->noHpPenanggungJawab,
            'no_bpjs' => $request->noBpjs,
        ];

        if ($pasien->where('id_user', $request->id)->update($data) && $user->where('id',$request->id)->update($data1)) {
            return response()->json([
                'success' => true,
                'message' => 'Selamat data profil anda sudah diinputkan'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi Bermasalah'
            ], 200);
        }


    }

    public function updatePassword(Request $request)
    {

        $user = User::findOrFail($request->id);

        if (Hash::check($request->passwordLama, $user->password)) {
            $user->fill([
                'password' => Hash::make($request->passwordBaru)
            ])->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Password lama yang anda masukan salah'
            ], 200);
        }


    }

    public function cekKondisiRawatJalan(Request $request){
        $cekNomorMr = Pasien::where('nomr', $request->nomorMr)->count();

        if ($cekNomorMr === 1) {
            $getNomor = Pasien::where('nomr', $request->nomorMr)->first();
            $tahunLahir = substr($getNomor->tgl_lahir, 0, 4);
            $pendaftaran = Pendaftaran::where('nomr',$request->nomorMr)->orderBy('created_at','desc')->get();

            if ($tahunLahir == $request->tahunLahir) {
                return [
                    'message' => 'Nomor MR Ditemukan',
                    'data' => $getNomor,
                    'dataBerobat' => $pendaftaran,
                    'image' => asset('img/profile/' . $getNomor['foto']),
                    'success' => true
                ];
            } else {
                return [
                    'message' => 'Tahun Lahir Salah',
                    'data' => [],
                    'success' => false
                ];
            }

        } else {
            return [
                'message' => 'Nomor MR Tidak Ditemukan',
                'data' => [],
                'success' => false
            ];
        }
    }
}
