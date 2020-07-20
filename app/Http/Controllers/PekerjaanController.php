<?php


namespace App\Http\Controllers;


use App\Pekerjaan;

class PekerjaanController extends Controller
{

    public function index()
    {
        $pekerjaan = Pekerjaan::groupBy('pekerjaan_nama')->orderBy('pekerjaan_nama')
            ->selectRaw('pekerjaan_nama')->get();

        return [
            'data' => $pekerjaan,
            'status' => 'ok'
        ];

    }

}
