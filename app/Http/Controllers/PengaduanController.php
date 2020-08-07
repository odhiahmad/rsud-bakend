<?php


namespace App\Http\Controllers;


use App\Pendaftaran;
use App\Pengaduan;
use App\User;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {

        $daftar = Pengaduan::where(['id_user' => $request->id])->orderBy('created_at', 'desc')->get();


        return [
            'data' => $daftar,
            'id' => $request->id,
            'status' => 'ok'
        ];

    }

    public function getPendaftaranSelesai(Request $request)
    {
        $getPendaftaran = Pendaftaran::where(['nomr' => $request->nomr, 'status_berobat' => 'Selesai'])->andWhere('status_berobat', 'Selesai Notif')->get();
        return response()->json([
            'data' => $getPendaftaran,
            'success' => true,
            'message' => 'Berhasil Mendapatkan data'
        ], 200);
    }

    public function inputPengaduan(Request $request)
    {
        $jumlahPengaduan = Pengaduan::where('id_user', $request->id_user)->count();

        $getUser = User::where('id', $request->id_user)->first();
        if ($jumlahPengaduan <= 50) {
            $pengaduan = new Pengaduan();
            $pengaduan->pengaduan_nomr = $request->nomorMr;
            $pengaduan->pengaduan_tanggal = date("Y-m-d");
            $pengaduan->pengaduan_namapasien = $request->nama;
            $pengaduan->pengaduan_alamatpasien = $request->alamat;
            $pengaduan->pengaduan_tgllahir = $request->tanggalLahir;
            $pengaduan->pengaduan_tempatrawat = $request->pengaduanTempatRawat;
            $pengaduan->pengaduan_jenislayanan = $request->pengaduanJenisLayanan;
            $pengaduan->pengaduan_namapelapor = $getUser['name'];
            $pengaduan->pengaduan_notelp = $request->no_telp;
            $pengaduan->pengaduan_hubungan = $request->pengaduanHubungan;
            $pengaduan->pengaduan_kronologis = $request->kronologis;
            $pengaduan->id_user = $request->id_user;

            if ($pengaduan->save()) {
                return response()->json([
                    'data' => $pengaduan,
                    'success' => true,
                    'message' => 'Berhasil menginputkan pengaduan'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi Bermasalah'
                ], 200);
            }
        } else {
            Pengaduan::where('id_user', $request->id_user)->orderBy('id', 'desc')->limit(1)->delete();

            $pengaduan = new Pengaduan();
            $pengaduan->id_user = $request->id_user;
            $pengaduan->pengaduan_nomr = $request->nomr;
            $pengaduan->pengaduan_tanggal = date("Y-m-d");
            $pengaduan->pengaduan_namapasien = $request->nama;
            $pengaduan->pengaduan_alamatpasien = $request->alamat;
            $pengaduan->pengaduan_tgllahir = $request->tanggalLahir;
            $pengaduan->pengaduan_tempatrawat = $request->pengaduanTempatRawat;
            $pengaduan->pengaduan_jenislayanan = $request->pengaduanJenisLayanan;
            $pengaduan->pengaduan_namapelapor = $getUser['name'];
            $pengaduan->pengaduan_notelp = $request->no_telp;
            $pengaduan->pengaduan_hubungan = $request->pengaduanHubungan;
            $pengaduan->pengaduan_kronologis = $request->kronologis;

            if ($pengaduan->save()) {
                return response()->json([
                    'data' => $pengaduan,
                    'success' => true,
                    'message' => 'Berhasil menginputkan pengaduan'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi Bermasalah'
                ], 200);
            }
        }


    }
}
