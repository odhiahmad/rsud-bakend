<?php


namespace App\Http\Controllers;


use App\BedMonitoring;
use App\Ketersedian;
use App\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BedMonitoringController extends Controller
{

    public function indexRuangan(){
        $ruangan = Ketersedian::with('getRuanganKetersedian')->groupBy('map_kamarid')
            ->selectRaw('sum(map_kapasitas) as total, sum(map_isipr) as perempuan, sum(map_isilk) as pria, map_kamarid')->get();

        return [
            'data'=> $ruangan,
            'status'=> 'ok'
        ];

    }
    public function index(Request $request){


        $bedMonitoring = Ketersedian::with('getKelasKetersedian')->where(['map_kamarid'=>$request->id])->get();


        return [
            'data' => $bedMonitoring,

        ];

    }
}
