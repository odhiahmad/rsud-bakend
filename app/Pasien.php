<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
class Pasien extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
        'tgl_lahir', 'nomr'
    ];

    protected $table = 'm_pasien';
}
