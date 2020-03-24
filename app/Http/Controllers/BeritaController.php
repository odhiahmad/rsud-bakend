<?php


namespace App\Http\Controllers;


use App\Berita;
use App\Faq;

use Illuminate\Http\Request;

class BeritaController extends Controller
{

    public function index(Request $request){
        $berita = Berita::orderBy('post_tanggal','desc');

        $searchValue = $request->input('search');


        if ($searchValue) {
            $berita->where(function ($berita) use ($searchValue) {
                $berita->where('post_judul', 'like', '%' . $searchValue . '%');
            });
        }

        $data = $berita->paginate(5);
        return [
            'data'=> $data,
            'status'=> 'ok'
        ];

    }
}
