<?php


namespace App\Http\Controllers;


use App\Negara;


class NegaraController extends Controller
{

    public function index()
    {
        $negara = Negara::select('nama_negara')->where('nama_negara','!=','Indonesia')->groupBy('nama_negara')->orderBy('nama_negara')
           ->get();

        return [
            'data' => $negara,
            'status' => 'ok'
        ];

    }

}

