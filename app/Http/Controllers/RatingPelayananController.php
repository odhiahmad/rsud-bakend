<?php


namespace App\Http\Controllers;


use App\Pasien;
use App\Pendaftaran;
use App\RatingPelayanan;
use Illuminate\Http\Request;

class RatingPelayananController extends Controller
{

    public function index(Request $request)
    {

        $daftar = RatingPelayanan::where(['id_user' => $request->id])->orderBy('created_at', 'desc')->get();


        return [
            'data' => $daftar,
            'id' => $request->id,
            'status' => 'ok'
        ];

    }

    public function inputPenilaian(Request $request)
    {

        $rating = new RatingPelayanan();
        $pendaftaran = new Pendaftaran();

        $dataRating = [
            'rating' => $request->rating,
            'catatan' => $request->catatan,
            'status' => 2
        ];

        $data = [
            'status_berobat' => 'Selesai',
        ];

        if ($rating->where('id', $request->idRating)->update($dataRating) && $pendaftaran->where('idx', $request->idPendaftaran)->update($data)) {
            return response()->json([
                'data' => $rating,
                'success' => true,
                'message' => 'Berhasil menginputkan penilaian'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi Bermasalah'
            ], 200);
        }


    }
}
