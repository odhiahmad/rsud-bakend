<?php


namespace App\Http\Controllers;
use App\Faq;
class FaqController extends Controller
{

    public function index(){
        $ruangan = Faq::all();

        return [
            'data'=> $ruangan,
            'status'=> 'ok'
        ];

    }
}
