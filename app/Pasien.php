<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $fillable = [
        'tgl_lahir', 'nomr'
    ];

    protected $table = 'm_pasien';
}
