<?php

namespace App\Http\Controllers;

use App\Models\asset;
use App\Models\device_type;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(){
        $assets = asset::with('deviceType')->paginate(12);
        return response()->json($assets);
    }
    public function getAsset(){
        $assets = asset::all();
        return response()->json($assets);
    }

    public  function getDeviceTypes(){
        $deviceTypes=device_type::all();
        return response()->json($deviceTypes);
    }


    public function editAsset($id)
    {
        $asset = asset::findOrFail($id);
        return response()->json($asset);
    }

    public function store(Request $request)
    {
        if ($request->filled('device_type_id')) {
            $deviceTypeId = $request->device_type_id;
        } else {
            $deviceType = device_type::firstOrCreate(['Name' => $request->device_type]);
            $deviceTypeId = $deviceType->id;
        }

        $asset = asset::create([
            'Device_Name' => $request->device_name,
            'Device_Type_Id' => $deviceTypeId,
            'Specification' => $request->specification,
            'Description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset saved successfully!',
            'data' => $asset,
        ], 201);
    }



    public function update(Request $request, $id)
    {
        $asset = asset::findOrFail($id);
        
        if ($request->filled('device_type_id')) {
            $deviceTypeId = $request->device_type_id;
        } else {
            $deviceType = device_type::firstOrCreate(['Name' => $request->device_type]);
            $deviceTypeId = $deviceType->id;
        }

        $asset->update([
            'Device_Name' => $request->device_name,
            'Device_Type_Id' => $deviceTypeId,
            'Specification' => $request->specification,
            'Description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset saved successfully!',
            'data' => $asset,
        ], 201);
    }



    public function deleteAsset($id)
    {
        $asset = asset::find($id);

        if ($asset) {
            $asset->delete();
            $response = [
                'success'   =>  true,
                'message'   =>  'Asset deleted successfully',
            ];
            return response()->json($response);
        }
    }
}
