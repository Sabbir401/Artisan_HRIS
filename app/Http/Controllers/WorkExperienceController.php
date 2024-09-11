<?php

namespace App\Http\Controllers;

use App\Models\work_experience;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $work = work_experience::all();

        return response()->json($work);
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
        $traning = work_experience::create([
            'EID' => $request->input('eid'),
            'Company_Name' => $request->input('companyName'),
            'Company_Business' => $request->input('companyBusiness'),
            'Company_Address' => $request->input('companyAddress'),
            'Designation' => $request->input('designation'),
            'Department' => $request->input('department'),
            'From_Date' => $request->input('fromDate'),
            'To_Date' => $request->input('toDate'),
            'Duration' => $request->input('duration'),
            'Responsibilities' => $request->input('jobRes'),
            'Last_Salary' => $request->input('lastSalary'),
            'Continuing' => $request->input('continuing'),
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
    public function show($id)
    {
        $work = work_experience::where('EID',$id)->get();;

        return response()->json($work);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $work = work_experience::find($id);

        return response()->json($work);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $work = work_experience::findorfail($id);
        $work->update([
            'Company_Name' => $request->input('companyName'),
            'Company_Business' => $request->input('companyBusiness'),
            'Company_Address' => $request->input('companyAddress'),
            'Designation' => $request->input('designation'),
            'Department' => $request->input('department'),
            'From_Date' => $request->input('fromDate'),
            'To_Date' => $request->input('toDate'),
            'Duration' => $request->input('duration'),
            'Responsibilities' => $request->input('jobRes'),
            'Last_Salary' => $request->input('lastSalary'),
            'Continuing' => $request->input('continuing'),
        ]);
        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully Updated',
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(work_experience $work_experience)
    {
        //
    }
}
