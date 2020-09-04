<?php


namespace App\Http\Controllers;


use App\JadwalDokter;
use App\Poly;
use App\ShuttleBus;
use App\ShuttleBusDetail;
use App\ShuttleBusPenumpang;
use App\ShuttleBusTrip;
use Illuminate\Http\Request;

class ShuttleBusController extends Controller
{

    public function indexShuttle()
    {
        $ruangan = ShuttleBus::all();

        return [
            'data' => $ruangan,
            'status' => 'ok'
        ];

    }

    public function cekKetersedianShuttle(Request $request){
        $jumlahTersedia = ShuttleBusPenumpang::where([
            'id_shuttle'=>$request->id_shuttle,
            'id_shuttle_rute'=>$request->id_shuttle_rute,
            'tanggal'=>$request->tanggal
        ])->count();

        if($jumlahTersedia <= 16){
            return [
                'status' => 1,
                'message' => 'Shuttle Bus Masih Tersedia'
            ];

        }else{
            return [
                'status' => 2,
                'message' => 'Kuota Penumpang Telah Penuh'
            ];

        }
    }

    public function indexShuttleDetail(Request $request)
    {

        date_default_timezone_set("Asia/Jakarta");
        $jamSkrg = date("H:i:s");
        $getDataAktif = ShuttleBusDetail::where('id_shuttle_bus', $request->id)->get();
        $getId = '';

        $tambah1 = strtotime("+20 minutes");
        $tambah2 = strtotime("-10 minutes");

        $jam1 = date("H:i", $tambah1);
        $jam2 = date("H:i", $tambah2);
        $jam3 = date("H:i");

        $jam1 .= ":00";
        $jam2 .= ":00";
        $jam3 .= ":00";

        for ($i = 0; $i < count($getDataAktif); $i++) {

            if (($jamSkrg > $getDataAktif[$i]['jam'] && $jamSkrg < $getDataAktif[$i + 1]['jam']) ||
                ($jamSkrg > $getDataAktif[$i]['jam'] && $jamSkrg > $getDataAktif[$i + 1]['jam'])) {
                if ($request->id == 1) {
                    if ($i + 1 == 17) {
                        $getId = 0;
                    } else if ($i + 1 == 34) {
                        $getId = 0;
                    } else if ($i + 1 == 51) {
                        $getId = 0;
                    } else {
                        $getId = $getDataAktif[$i]['id_trip'];
                    }
                } else if ($request->id == 2) {
                    if ($i + 1 == 15) {
                        $getId = 0;
                    } else if ($i + 1 == 30) {
                        $getId = 0;
                    } else if ($i + 1 == 45) {
                        $getId = 0;
                    } else {
                        $getId = $getDataAktif[$i]['id_trip'];
                    }
                }
            } else if ($jamSkrg >= $getDataAktif[$i]['jam']) {
                if ($request->id == 1) {
                    $getId = 0;
                } else if ($request->id == 2) {
                    $getId = 0;
                }
            }


        }
        if ($getId != 0) {

            $data = ShuttleBusDetail::where(['id_shuttle_bus' => $request->id, 'id_trip' => $getId])->get();
            return [
                'data' => $data,

            ];
        } else {
            return [
                'data' => [],
            ];


        }
    }

    public function shuttleDetail(Request $request)
    {
        $shuttleDetail = ShuttleBusDetail::where(['id_shuttle_bus' => $request->id_shuttle, 'id_trip' => $request->id_trip])->get();
        return [
            'data' => $shuttleDetail,
            'status' => 'ok'

        ];
    }

    public
    function shuttleRute(Request $request)
    {
        $shuttleDetail = ShuttleBusTrip::where(['id_shuttle_bus' => $request->id_shuttle])->get();
        return [
            'data' => $shuttleDetail,
            'status' => 'ok'

        ];
    }

    public
    function shuttleBusPenumpang(Request $request)
    {
        $tanggal = date('Y-m-d');
        $shuttlePenumpang = ShuttleBusPenumpang::where([
            'id_shuttle' => $request->id_shuttle,
            'id_shuttle_rute' => $request->id_trip,
            'tanggal' => $tanggal
        ])->groupBy('tempat_tunggu')
            ->selectRaw('count(id_pendaftaran) as total, tempat_tunggu')->get();

        return [
            'data' => $shuttlePenumpang,
            'status' => 'ok'

        ];
    }
}
