<?php

namespace App\Http\Controllers;

use App\Models\child;
use App\Models\nominee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NomineeController extends Controller
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
            'nomineeName' => 'required',
            'dob' => 'required',
            'contactNo' => 'required',
        ], [
            'nomineeName.required' => 'The Nominee Name is required.',
            'dob.required' => 'The Nominee Date of Birth is required.',
            'contactNo.required' => 'The Contact No is required.',
        ]);

        $store = nominee::create([
            'EID' => $request->input('eid'),
            'Nominee_Name' => $request->input('nomineeName'),
            'DOB' => $request->input('dob'),
            'Contact_No' => $request->input('contactNo'),
            'Email' => $request->input('email'),
            'NID' => $request->input('nid'),
            'Share' => $request->input('share'),
            'Personal_Address' => $request->input('presentAddress'),
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
    public function show(nominee $nominee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $nominee = nominee::where('EID', $id)->first();

        if (!$nominee) {
            return response()->json(['message' => 'Nominee not found']);
        }
        return response()->json($nominee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $nominee = DB::table('nominees')
            ->select('id')
            ->where('EID', $id)
            ->first();

        if ($nominee) {
            $nominee->update([
                'Nominee_Name' => $request->input('nomineeName'),
                'DOB' => $request->input('dob'),
                'Contact_No' => $request->input('contactNo'),
                'Email' => $request->input('email'),
                'NID' => $request->input('nid'),
                'Share' => $request->input('share'),
                'Personal_Address' => $request->input('presentAddress'),
            ]);

            $response = [
                'success' => true,
                'message' => 'Updated Successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Nominee not found'
            ];
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(nominee $nominee)
    {
        //
    }
}
