<?php

namespace App\Http\Controllers;

use App\Models\child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function show($id)
    {
        $children = child::where('EID', $id)->get();
        return response()->json($children);
    }

    public function edit($id)
    {
        $child = child::find($id);
        return response()->json($child);
    }

    public function store(Request $request)
    {
        $store = child::create([
            'EID' => $request->input('eid'),
            'Child_Name' => $request->input('childName'),
            'NID' => $request->input('nid'),
            'Email' => $request->input('email'),
            'Contact_No' => $request->input('contactNo'),
            'DOB' => $request->input('dob'),
        ]);

        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully inserted',
        ];

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $children = child::findorfail($id);
        if ($children) {
            $children->update([
                'Child_Name' => $request->input('childName'),
                'NID' => $request->input('nid'),
                'Email' => $request->input('email'),
                'Contact_No' => $request->input('contactNo'),
                'DOB' => $request->input('dob'),
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
}
