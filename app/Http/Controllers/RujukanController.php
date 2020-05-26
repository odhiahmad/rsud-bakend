<?php


namespace App\Http\Controllers;


use App\Agama;
use App\Rujukan;

class RujukanController extends Controller
{

    public function index()
    {
        $rujukan = Rujukan::all();

        return [
            'data' => $rujukan,
            'status' => 'ok'
        ];

    }

}
