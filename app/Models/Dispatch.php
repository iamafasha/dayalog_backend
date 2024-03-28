<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Traits\Dispatch as DispatchTraits;
class Dispatch extends Model
{
    use HasFactory, SoftDeletes, DispatchTraits;

    protected $guarded = [];



    public function orderVehicle()
    {
        return $this->belongsTo(OrderVehicle::class);
    }

    //Faked relastionships( Cannot use where has)
    public function vehicle()
    {
       return $this->orderVehicle->vehicle();
    }


    public function  device(){
         return $this->vehicle->device();
    }

    public function deviceEvents()
    {
        return $this->hasMany(DispatchedDeviceEvents::class);
    }

    public function stops()
    {
        return $this->deviceEvents()->where('status','stopped');
    }


    public function devicePositions()
    {
        return $this->belongsToMany(DevicePosition::class);
    }

}
