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
        $ruangan = ShuttleBus::all();

        return [
            'data'=> $ruangan,
            'status'=> 'ok'
        ];

    }
    public function indexShuttleDetail(Request $request){

        date_default_timezone_set("Asia/Jakarta");
        $jamSkrg = date("h:i:s");
        $getDataAktif = ShuttleBusDetail::where('id_shuttle_bus',$request->id)->get();
        $getId = '';

        for($i = 0;$i<count($getDataAktif);$i++){

            if($jamSkrg > $getDataAktif[$i]['jam'] && $jamSkrg < $getDataAktif[$i+1]['jam']){
                if($request->id == 1){
                    if($i+1 == 17){
                        $getId = 2;
                    }else if($i+1 == 34){
                        $getId = 3;
                    }else if($i+1 == 34){
                        $getId = 1;
                    }else{
                        $getId = $getDataAktif[$i]['id_trip'];
                    }
                }else if($request->id == 2){
                    if($i+1 == 15){
                        $getId = 5;
                    }else if($i+1 == 30){
                        $getId = 6;
                    }else if($i+1 == 45){
                        $getId = 4;
                    }else{
                        $getId = $getDataAktif[$i]['id_trip'];
                    }
                }


            }
        }
        $data = ShuttleBusDetail::where(['id_shuttle_bus'=>$request->id,'id_trip'=>$getId])->orderBy('jam')->get();


        return [
            'data' => $data,
            'jam'=>$jamSkrg

        ];

    }
}
