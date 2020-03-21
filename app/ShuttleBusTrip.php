<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class ShuttleBusTrip extends Model
{
    protected $table = 'tb_shuttle_bus_trip';

    public function getShuttle() {
        return $this->hasMany('App\ShuttleBus',"id","id_shuttle_bus");
    }

}

