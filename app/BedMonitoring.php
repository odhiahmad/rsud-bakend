<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class BedMonitoring extends Model
{
    protected $fillable = [
        'name', 'price', 'quantity'
    ];

    protected $table = 'tb_bed_monitoring';

}
