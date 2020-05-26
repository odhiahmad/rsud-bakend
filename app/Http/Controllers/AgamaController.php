<?php


namespace App\Http\Controllers;


use App\Agama;
use App\Bahasa;

class AgamaController extends Controller
{

    public function index()
    {
        $agama = Agama::groupBy('agama')->orderBy('agama')
            ->selectRaw('agama')->get();

        return [
            'data' => $agama,
            'status' => 'ok'
        ];

    }

}
