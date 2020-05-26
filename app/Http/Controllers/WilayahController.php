<?php


namespace App\Http\Controllers;


use App\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{

    public function indexProvinsi()
    {
        $provinsi = Wilayah::groupBy('provinsi')->orderBy('provinsi')
            ->selectRaw('provinsi')->get();

        return [
            'data' => $provinsi,
            'status' => 'ok'
        ];

    }

    public function indexKota(Request $request)
    {
        $kota = Wilayah::where(['provinsi' => $request->provinsi])->groupBy('nama_kabkota')->orderBy('nama_kabkota')
        ->selectRaw('nama_kabkota')->get();

        return [
            'data' => $kota,
            'status' => 'ok'
        ];

    }

    public function indexKecamatan(Request $request)
    {
        $kecamatan = Wilayah::where([
            'nama_kabkota' => $request->kota,
            'provinsi' => $request->provinsi])->groupBy('kecamatan')->orderBy('kecamatan')->selectRaw('kecamatan')->get();

        return [
            'data' => $kecamatan,
            'status' => 'ok'
        ];

    }

    public function indexDesa(Request $request)
    {
        $desa = Wilayah::where([
            'kecamatan' => $request->kecamatan,
            'provinsi' => $request->provinsi,
            'nama_kabkota' => $request->kota
            ])->groupBy('desa')->orderBy('desa')->selectRaw('desa')->get();

        return [
            'data' => $desa,
            'status' => 'ok'
        ];

    }
}
