<?php


namespace App\Http\Controllers;


use App\JadwalDokter;
use App\Ketersedian;
use App\Poly;
use Illuminate\Http\Request;

class PolyController extends Controller
{

    public function indexPoly(){
        $ruangan = Poly::where(['poly_status'=>'Aktif'])->get();

        return [
            'data'=> $ruangan,
            'status'=> 'ok'
        ];

    }
    public function indexPolyDetail(Request $request){


        $bedMonitoring = JadwalDokter::with('getDokterJadwal')->where('jadwal_poly_id',$request->id)->orderBy('jadwal_hari')->get();


        return [
            'data' => $bedMonitoring,

        ];

    }
}

