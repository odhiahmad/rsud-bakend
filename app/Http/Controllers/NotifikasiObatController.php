<?php


namespace App\Http\Controllers;

use App\Obat;
use App\ObatDetail;
use App\Pendaftaran;
use Illuminate\Http\Request;

class NotifikasiObatController extends Controller
{

    public function index(Request $request)
    {
        $daftar = Pendaftaran::where(['idUserDaftar'=> $request->id,'status_berobat'=>'Selesai'])->orderBy('created_at', 'desc');

        $data = $daftar->paginate(10);
        return [
            'data' => $data,
            'status' => 'ok',
        ];

    }

    public function indexDetail(Request $request)
    {
        $obat = Obat::where(['IDPENDAFTARAN'=>$request->id])->first();
        $obatDetail = ObatDetail::where(['KDJL'=>$obat->KDJL])->get();


        return [
            'data' => $obatDetail,
            'status' => 'ok'
        ];

    }

}


