<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'tbl02_pendaftaran';

    public function getUserPendaftaran() {
        return $this->hasMany('App\Pasien',"nomr","idUserDaftar");
    }

    public function getUserRating() {
        return $this->hasOne('App\RatingPelayanan',"id_pendaftaran","idx");
    }

}

