<?php


namespace App\Http\Controllers;


use App\JadwalDokter;
use App\Ketersedian;
use App\Pendaftaran;
use App\Poly;
use Illuminate\Http\Request;

class PolyController extends Controller
{

    public function indexPoly()
    {
        $ruangan = Poly::where(['poly_status' => 'Aktif'])->get();

        return [
            'data' => $ruangan,
            'status' => 'ok'
        ];

    }

    public function indexPolyPengaduan()
    {
        $ruangan = Poly::all();

        return [
            'data' => $ruangan,
            'status' => 'ok'
        ];

    }


    public function indexPolyDetail(Request $request)
    {


        $bedMonitoring = JadwalDokter::with('getDokterJadwal')->where('jadwal_poly_id', $request->id)->orderBy('jadwal_hari')->get();


        return response()->json([
            'data' => $bedMonitoring,
            'urlImage' => asset('img/dokter/'),

        ]);

    }

    public function generateJam()
    {
        $jadwalDokter = new JadwalDokter();


        $jadwal = [
            [
                'id' => 0,
                'name' => "08:00:00 - 09:00:00",
                'jam' => "08:00:00"
            ],
            [
                'id' => 1,
                'name' => "09:00:00 - 10:00:00",
                'jam' => "09:00:00"
            ],
            [
                'id' => 2,
                'name' => "10:00:00 - 11:00:00",
                'jam' => "10:00:00"
            ],
            [
                'id' => 3,
                'name' => "11:00:00 - 12:00:00",
                'jam' => "11:00:00"
            ],
            [
                'id' => 4,
                'name' => "13:00:00 - 14:00:00",
                'jam' => "13:00:00"
            ],
            [
                'id' => 5,
                'name' => "14:00:00 - 15:00:00",
                'jam' => "14:00:00"
            ],
            [
                'id' => 6,
                'name' => "15:00:00 - 16:00:00",
                'jam' => "15:00:00"
            ],
        ];

        $data = [
            'jadwal_tes' => json_encode($jadwal)
        ];


        if($jadwalDokter->where('jadwal_status','Aktif')->update($data)){
            return[
                'data'=>'Berhasil'
            ];
        }else{
            return[
                'data'=>'Gagal',
                'tes'=>$jadwal
            ];
        }
    }

    public function indexPolyDetailHari(Request $request)
    {
        $tanggal = substr($request->tanggalKunjungan, 0, 10);

        $cekDaftar = Pendaftaran::where(['id_ruang'=>$request->id,'tanggal_kunjungan'=>$tanggal])->get();
        $bedMonitoring = JadwalDokter::with('getDokterJadwal')->where([
            'jadwal_poly_id' => $request->id,
            'jadwal_hari' => $request->hari
        ])->orderBy('jadwal_hari')->get();


        return [
            'dataJam'=>$cekDaftar,
            'data' => $bedMonitoring,

        ];

    }
}

