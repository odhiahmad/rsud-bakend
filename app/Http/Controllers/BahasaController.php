<?php


namespace App\Http\Controllers;


use App\Bahasa;


class BahasaController extends Controller
{

    public function index()
    {
        $bahasa = Bahasa::groupBy('bahasa_nama')->orderBy('bahasa_nama')
            ->selectRaw('bahasa_nama')->get();

        return [
            'data' => $bahasa,
            'status' => 'ok'
        ];

    }

}
