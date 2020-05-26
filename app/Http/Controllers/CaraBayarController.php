<?php


namespace App\Http\Controllers;



use App\CaraBayar;

class CaraBayarController extends Controller
{

    public function index()
    {
        $agama = CaraBayar::get();

        return [
            'data' => $agama,
            'status' => 'ok'
        ];

    }

}
