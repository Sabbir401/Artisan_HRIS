<?php

namespace App\Http\Controllers;

use App\Models\asset;
use App\Models\employee;
use Illuminate\Http\Request;
use App\Models\employee_asset;
use Illuminate\Support\Facades\Session;

class EmployeeAssetController extends Controller
{
    public function index(){
        $employee_assets = employee_asset::select('employee_assets.id','departments.id as Dept_Id', 'departments.Name as Dept_Name', 'employees.Full_Name', 'assets.Device_Name', 'employee_assets.Date', 'employee_assets.Quantity', 'employee_assets.Serial_Number')
        ->join('employees', 'employees.id', '=', 'employee_assets.EID')
        ->join('officials', 'employees.id', '=', 'officials.EID')
        ->join('departments', 'officials.Department_Id', '=', 'departments.id')
        ->join('assets', 'employee_assets.Device_Id', '=', 'assets.id')
        ->get();
        return response()->json($employee_assets);
    }


    public function allAsset(){
        $assets = asset::all();
        return response()->json($assets);
    }

    public function store(Request $request){
        $userId = Session::get('User_Id');
        $asset = employee_asset::create([
            'EID' => $request->employee_Id,
            'Device_Id' => $request->device_id,
            'Date' => $request->date,
            'Serial_Number' => $request->serialNumber,
            'Quantity' => $request->quantity,
            'created_by' => $userId,
            'updated_by' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset saved successfully!',
            'data' => $asset,
        ], 201);
    }


    public function editEmpAsset($id){
        $employee_assets = employee_asset::select('employee_assets.id', 'departments.id as Dept_Id', 'employees.id as Emp_Id', 'assets.id as Asset_Id', 'employee_assets.Date', 'employee_assets.Quantity', 'employee_assets.Serial_Number')
        ->join('employees', 'employees.id', '=', 'employee_assets.EID')
        ->join('officials', 'employees.id', '=', 'officials.EID')
        ->join('departments', 'officials.Department_Id', '=', 'departments.id')
        ->join('assets', 'employee_assets.Device_Id', '=', 'assets.id')
        ->where('employee_assets.id', $id)
        ->first();

        return response()->json($employee_assets);
    }

    public function update(Request $request, $id){
        $userId = Session::get('User_Id');
        $employee_asset = employee_asset::find($id);

        $employee_asset->update([
            'EID' => $request->employee_Id,
            'Device_Id' => $request->device_id,
            'Date' => $request->date,
            'Serial_Number' => $request->serialNumber,
            'Quantity' => $request->quantity,
            'updated_by' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset saved successfully!'
        ], 201);
    }
}
