<?php

namespace App\Http\Controllers;

use App\Models\employee;
use App\Models\official;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OfficialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'departmentId' => 'required',
            'designationId' => 'required',
            'employeeGrade' => 'required',
            'employeeTypeId' => 'required',
        ], [
            'departmentId.required' => 'The Department Name is required.',
            'designationId.required' => 'The Designation is required.',
            'employeeGrade.required' => 'The Employee Grade is required.',
            'employeeTypeId.required' => 'The Employee Type is required.',
        ]);

        $userId = Session::get('User_Id');
        $store = official::create([
            'EID' => $request->input('eid'),
            'Department_Id' => $request->input('departmentId'),
            'Designation_Id' => $request->input('designationId'),
            'Employee_Grade' => $request->input('employeeGrade'),
            'Area_Id' => $request->input('areaId'),
            'Territory_Id' => $request->input('territoryId'),
            'Employee_type_Id' => $request->input('employeeTypeId'),
            'Supervisor_Id' => $request->input('supervisorId'),
            'DOJ' => $request->input('doj'),
            'DOL' => $request->input('dol'),
            'Provation_period' => $request->input('provationPeriod'),
            'DOC' => $request->input('doc'),
            'Job_Location_Id' => $request->input('jobLocation'),
            'Shift' => $request->input('shift'),
            'Status' => $request->input('status'),
            'created_by' => $userId,
            'updated_by' => 0,
        ]);

        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully inserted',
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(official $official)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $official = official::with([
            'employee',
            'area',
            'department',
            'designation',
            'employeeType',
            'territory',
            'supervisor',
            'branch'

        ])->where('EID', $id)->first();

        if (!$official) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($official);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $official_id = DB::table('officials')
        ->select('id')
        ->where('EID', $id)
        ->first();

        $userId = Session::get('User_Id');
        
        $official = official::find($official_id->id);
        DB::beginTransaction();
        $official->update([
            'EID' => $request->input('eid'),
            'Department_Id' => $request->input('departmentId'),
            'Designation_Id' => $request->input('designationId'),
            'Employee_Grade' => $request->input('employeeGrade'),
            'Area_Id' => $request->input('areaId'),
            'Territory_Id' => $request->input('territoryId'),
            'Employee_type_Id' => $request->input('employeeTypeId'),
            'Supervisor_Id' => $request->input('supervisorId'),
            'DOJ' => $request->input('doj'),
            'DOL' => $request->input('dol'),
            'Provation_period' => $request->input('provationPeriod'),
            'DOC' => $request->input('doc'),
            'Job_Location_Id' => $request->input('jobLocation'),
            'Shift' => $request->input('shift'),
            'Status' => $request->input('status'),
            'updated_by' => $userId,
        ]);
        $response = [
            'success' => true,
            'message'  => 'Updated Successfully'
        ];
        
        DB::commit();
        return response()->json($response);
        
        DB::rollback();
        $response = [
            'success'   =>  false,
            'message'   =>  'Error while inserting',
        ];
        return response()->json($response);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(official $official)
    {
        //
    }
}
