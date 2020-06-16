<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'tbl04_penjualan';

    public function getObatDetail() {
        return $this->hasMany('App\ObatDetail',"KDJL","KDJL");
    }
}
