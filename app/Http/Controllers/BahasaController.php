<?php


namespace App\Http\Controllers;


use App\Bahasa;


class BahasaController extends Controller
{

    public function index()
    {
        $bahasa = Bahasa::groupBy('bahasa_nama')->orderByRaw("FIELD(bahasa_nama , 'Bahasa Minang/ Minangkabau/ Padang','Melayu') ASC")
            ->selectRaw('bahasa_nama')->get();

        return [
            'data' => $bahasa,
            'status' => 'ok'
        ];

    }

}
