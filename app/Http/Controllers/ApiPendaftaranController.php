<?php


namespace App\Http\Controllers;


use App\Agama;
use App\Bahasa;
use App\CaraBayar;
use App\Negara;
use App\Pasien;
use App\Pekerjaan;
use App\Poly;
use App\Suku;
use App\Wilayah;
use Illuminate\Http\Request;

class ApiPendaftaranController extends Controller
{
    public function indexStep1()
    {
        $agama = Agama::groupBy('agama')->orderBy('agama')
            ->selectRaw('agama')->get();
        $pekerjaan = Pekerjaan::groupBy('pekerjaan_nama')->orderBy('pekerjaan_nama')
            ->selectRaw('pekerjaan_nama')->get();
        return [
            'dataAgama' => $agama,
            'dataPekerjaan' => $pekerjaan,
            'status' => 'ok'
        ];

    }

    public function indexStep2()
    {
        $provinsi = Wilayah::groupBy('provinsi')->orderByRaw("FIELD(provinsi , 'Sumatera Barat') DESC")
            ->selectRaw('provinsi')->get();
        $bahasa = Bahasa::groupBy('bahasa_nama')->orderByRaw("FIELD(bahasa_nama , 'Bahasa Minang/ Minangkabau/ Padang','Melayu') DESC")
            ->selectRaw('bahasa_nama')->get();
        $negara = Negara::select('nama_negara')->where('nama_negara', '!=', 'Indonesia')->groupBy('nama_negara')->orderBy('nama_negara')
            ->get();
        $suku = Suku::groupBy('suku_nama')->orderByRaw("FIELD(suku_nama , 'Minangkabau') DESC")
            ->selectRaw('suku_nama')->get();
        return [
            'dataProvinsi' => $provinsi,
            'dataBahasa' => $bahasa,
            'dataNegara' => $negara,
            'dataSuku' => $suku,
            'status' => 'ok'
        ];

    }

    public function indexEditProfil(Request $request)
    {

        $getPasien = Pasien::where('id_user', $request->input('id'))->first();

        $agama = Agama::groupBy('agama')->orderBy('agama')
            ->selectRaw('agama')->get();
        $pekerjaan = Pekerjaan::groupBy('pekerjaan_nama')->orderBy('pekerjaan_nama')
            ->selectRaw('pekerjaan_nama')->get();
        $provinsi = Wilayah::groupBy('provinsi')->orderByRaw("FIELD(provinsi , 'Sumatera Barat') DESC")
            ->selectRaw('provinsi')->get();
        $bahasa = Bahasa::groupBy('bahasa_nama')->orderByRaw("FIELD(bahasa_nama , 'Bahasa Minang/ Minangkabau/ Padang','Melayu') DESC")
            ->selectRaw('bahasa_nama')->get();
        $negara = Negara::select('nama_negara')->where('nama_negara', '!=', 'Indonesia')->groupBy('nama_negara')->orderBy('nama_negara')
            ->get();
        $suku = Suku::groupBy('suku_nama')->orderByRaw("FIELD(suku_nama , 'Minangkabau') DESC")
            ->selectRaw('suku_nama')->get();
        return response()->json([
            'dataProfile' => $getPasien,
            'dataAgama' => $agama,
            'dataPekerjaan' => $pekerjaan,
            'dataProvinsi' => $provinsi,
            'dataBahasa' => $bahasa,
            'dataNegara' => $negara,
            'dataSuku' => $suku,
            'status' => 'ok'
        ], 200);

    }

    public function indexLengkapiProfil(Request $request)
    {

        $getPasien = Pasien::where('nomr', $request->input('nomorMr'))->first();

        $agama = Agama::groupBy('agama')->orderBy('agama')
            ->selectRaw('agama')->get();
        $pekerjaan = Pekerjaan::groupBy('pekerjaan_nama')->orderBy('pekerjaan_nama')
            ->selectRaw('pekerjaan_nama')->get();
        $provinsi = Wilayah::groupBy('provinsi')->orderByRaw("FIELD(provinsi , 'Sumatera Barat') DESC")
            ->selectRaw('provinsi')->get();
        $bahasa = Bahasa::groupBy('bahasa_nama')->orderByRaw("FIELD(bahasa_nama , 'Bahasa Minang/ Minangkabau/ Padang','Melayu') DESC")
            ->selectRaw('bahasa_nama')->get();
        $negara = Negara::select('nama_negara')->where('nama_negara', '!=', 'Indonesia')->groupBy('nama_negara')->orderBy('nama_negara')
            ->get();
        $suku = Suku::groupBy('suku_nama')->orderByRaw("FIELD(suku_nama , 'Minangkabau') DESC")
            ->selectRaw('suku_nama')->get();
        return response()->json([
            'dataProfile' => $getPasien,
            'dataAgama' => $agama,
            'dataPekerjaan' => $pekerjaan,
            'dataProvinsi' => $provinsi,
            'dataBahasa' => $bahasa,
            'dataNegara' => $negara,
            'dataSuku' => $suku,
            'status' => 'ok'
        ], 200);

    }

    public function indexPendaftaranOnlineBaru()
    {
        $poly = Poly::where(['poly_status' => 'Aktif'])->get();
        $caraBayar = CaraBayar::get();
        $agama = Agama::groupBy('agama')->orderBy('agama')
            ->selectRaw('agama')->get();
        $pekerjaan = Pekerjaan::groupBy('pekerjaan_nama')->orderBy('pekerjaan_nama')
            ->selectRaw('pekerjaan_nama')->get();
        $provinsi = Wilayah::groupBy('provinsi')->orderByRaw("FIELD(provinsi , 'Sumatera Barat') DESC")
            ->selectRaw('provinsi')->get();
        $bahasa = Bahasa::groupBy('bahasa_nama')->orderByRaw("FIELD(bahasa_nama , 'Bahasa Minang/ Minangkabau/ Padang','Melayu') DESC")
            ->selectRaw('bahasa_nama')->get();
        $negara = Negara::select('nama_negara')->where('nama_negara', '!=', 'Indonesia')->groupBy('nama_negara')->orderBy('nama_negara')
            ->get();
        $suku = Suku::groupBy('suku_nama')->orderByRaw("FIELD(suku_nama , 'Minangkabau') DESC")
            ->selectRaw('suku_nama')->get();
        return response()->json([
            'dataAgama' => $agama,
            'dataPekerjaan' => $pekerjaan,
            'dataProvinsi' => $provinsi,
            'dataBahasa' => $bahasa,
            'dataNegara' => $negara,
            'dataSuku' => $suku,
            'dataCaraBayar' => $caraBayar,
            'dataPoly' => $poly,
            'status' => 'ok'
        ], 200);

    }
}
