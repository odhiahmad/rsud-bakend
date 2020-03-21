<?php


namespace App\Http\Controllers;


use App\JadwalDokter;
use App\Poly;
use App\ShuttleBus;
use App\ShuttleBusDetail;
use App\ShuttleBusTrip;
use Illuminate\Http\Request;

class ShuttleBusController extends Controller
{

    public function indexShuttle(){
        $ruangan = ShuttleBusTrip::with('getShuttle')->get();

        return [
            'data'=> $ruangan,
            'status'=> 'ok'
        ];

    }
    public function indexShuttleDetail(Request $request){


        $data = ShuttleBusDetail::where(['id_shuttle_bus'=>$request->id,'id_trip'=>$request->idTrip])->orderBy('jam')->get();


        return [
            'data' => $data,

        ];

    }
}
