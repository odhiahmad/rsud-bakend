<?php


namespace App\Http\Controllers;


use App\Pendaftaran;
use App\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController
{
    public function index(Request $request)
    {
        $daftar = Pengaduan::where('id_user', $request->id)->orderBy('created_at', 'desc');

        $searchValue = $request->input('search');


        if ($searchValue) {
            $daftar->where(function ($daftar) use ($searchValue) {
                $daftar->where('pengaduan_kronologis', 'like', '%' . $searchValue . '%');
            });
        }

        $data = $daftar->paginate(5);
        return [
            'data'=> $data,
            'status'=> 'ok'
        ];

    }
}
