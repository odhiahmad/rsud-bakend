<?php


namespace App\Http\Controllers;


use App\Suku;
use App\Wilayah;

class SukuController extends Controller
{

    public function index()
    {
        $suku = Suku::groupBy('suku_nama')->orderBy('suku_nama')
            ->selectRaw('suku_nama')->get();

        return [
            'data' => $suku,
            'status' => 'ok'
        ];

    }

}
