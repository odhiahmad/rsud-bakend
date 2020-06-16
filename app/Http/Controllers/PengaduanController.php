<?php


namespace App\Http\Controllers;


use App\Pendaftaran;
use App\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {

        $daftar = Pengaduan::where(['id_user'=>$request->id])->orderBy('created_at', 'desc')->get();


        return [
            'data' => $daftar,
            'id'=>$request->id,
            'status' => 'ok'
        ];

    }

    public function inputPengaduan(Request $request)
    {
        $jumlahPengaduan = Pengaduan::where('id_user', $request->id_user)->count();

        if ($jumlahPengaduan <= 50) {
            $pengaduan = new Pengaduan();
            $pengaduan->id_user = $request->id_user;
            $pengaduan->pesan_pengaduan = $request->pengaduan;

            if ($pengaduan->save()) {
                return response()->json([
                    'data'=>$pengaduan,
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
            $pengaduan->pesan_pengaduan = $request->pengaduan;

            if ($pengaduan->save()) {
                return response()->json([
                    'data'=>$pengaduan,
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
