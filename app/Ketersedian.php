<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Ketersedian extends Model
{
    protected $table = 'tb_ketersediaan';

    public function getKelasKetersedian() {
        return $this->hasMany('App\Kelas',"kelas_id","map_kelasid");
    }

    public function getRuanganKetersedian() {
        return $this->hasMany('App\Ruangan',"grId","map_kamarid");
    }
}
