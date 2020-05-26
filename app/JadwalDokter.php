<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    protected $fillable = [
        'jadwal', 'jadwal_status'
    ];
    protected $table = 'trx_jadwal_dokter';

    public function getDokterJadwal() {
        return $this->hasMany('App\Dokter',"dokter_id","jadwal_dokter_id");
    }
}
