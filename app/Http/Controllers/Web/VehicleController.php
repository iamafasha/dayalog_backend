<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Device;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleDriver;
use App\Services\VehicleService;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;

        $vehicles = Vehicle::with(['driver.bioData' ])
                    ->where('number_plate', 'LIKE', "%$term%")
                    ->orWhere('make', 'LIKE', "%$term%")
                    ->orWhere('model', 'LIKE', "%$term%")
                    ->paginate(7);
        return Inertia::render('ViewVehicles', [
            'vehicles' => $vehicles
        ]);
    }
    public function create(Request $request)
    {
        $free_devices = Device::with('vehicle')->whereDoesntHave('vehicle')->get();
        return Inertia::render('AddVehicle', [
            'devices' => $free_devices,
        ]);
    }

    public function store(VehicleRequest $request)
    {
        (new VehicleService())->store($request->validated());

        return redirect()->back()->with('success', 'Added Vehicle');
    }
    public function attachDriver(Request $request, Vehicle $vehicle)
    {

        if($vehicle->driver != null ){
            return  redirect()->back()->with('success', 'Vehicle is already attached to a driver.');
        }

        VehicleDriver::create(
            [
                'vehicle_id' => $vehicle->id,
                'driver_id' => $request->driver_id
            ]
        );

        return redirect()->back()->with('success', 'Vehicle updated');
    }

    public function detachDriver(Request $request)
    {


        $vehicleDriver = VehicleDriver::where('vehicle_id', $request->vehicle_id)
            ->where('driver_id', $request->driver_id)
            ->first();

        if (!$vehicleDriver) {
            return redirect()->back()->with('message' , 'Vehicle not assigned to the driver.');
        }

        $vehicleDriver->delete();

        return redirect()->back()->with('success', 'Vehicle updated');
    }
}
