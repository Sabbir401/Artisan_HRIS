<?php

namespace App\Http\Controllers;

use App\Models\holiday;
use App\Models\leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{

    // function __construct()
    // {
    //     $this->middleware(['permission:create user'], ['only' => ['create', 'store']]);
    //     $this->middleware(['permission:user list'], ['only' => ['index', 'show', 'leaveSummery']]);
    // }


    public function index()
    {
        $leave = leave::get();
        return response()->json($leave);
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
            'Employee_Id' => 'required',
            'From_Date' => 'required',
            'To_Date' => 'required',
            'Leave_Type_Id' => 'required',
        ], [
            'Employee_Id.required' => 'The Employee ID is required.',
            'Leave_Type_Id.required' => 'The Leave Type is required.',
            'From_Date.unique' => 'The Form Date is required.',
            'To_Date.required' => 'The To Date is required.',
        ]);

        $file_path = null;

        DB::beginTransaction();
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,xlsx,pdf|max:512'
            ]);

            $file_name = time() . '_' . $request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
            $leave = leave::create([
                'EID' => $request->input('Employee_Id'),
                'Leave_Type_Id' => $request->input('Leave_Type_Id'),
                'From_Date' => $request->input('From_Date'),
                'To_Date' => $request->input('To_Date'),
                'Purpose' => $request->input('Purpose'),
                'Status' => $request->input('Status'),
                'Attachment_Url' => '/storage/' . $file_path,
            ]);
        }else{
            $leave = leave::create([
                'EID' => $request->input('Employee_Id'),
                'Leave_Type_Id' => $request->input('Leave_Type_Id'),
                'From_Date' => $request->input('From_Date'),
                'To_Date' => $request->input('To_Date'),
                'Purpose' => $request->input('Purpose'),
                'Status' => $request->input('Status'),
            ]);
        }

        //      FIND Days Between Dates
        // $startDate = new \DateTime($request->From_Date);
        // $endDate = new \DateTime($request->To_Date);

        // $interval = $startDate->diff($endDate);
        // $daysBetween = $interval->days;
        // dd($daysBetween);

        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully inserted',
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $leaves = leave::with('leave_type')->where('EID', $id)->get();

        foreach ($leaves as $leave) {
            $startDate = new \DateTime($leave->From_Date);
            $endDate = new \DateTime($leave->To_Date);

            $interval = $startDate->diff($endDate);
            $leave->daysBetween = $interval->days + 1;
        }

        return response()->json($leaves);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(leave $id) {}

    public function leaveSummery($id)
    {
        $leaves = leave::with('leave_type')->where('EID', $id)->get();

        foreach ($leaves as $leave) {
            $startDate = new \DateTime($leave->From_Date);
            $endDate = new \DateTime($leave->To_Date);

            $interval = $startDate->diff($endDate);
            $leave->daysBetween = $interval->days;
        }

        return response()->json($leaves);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $leave = leave::find($id);
        $leave->update([
            'Status' => $request->input('Status'),
        ]);

        return response()->json('Updated Successfully');
    }


    public function destroy(leave $leave)
    {
        //
    }


    //Holiday Functions

    public function fetchHoliday()
    {
        $holidays = holiday::all();
        return response()->json($holidays);
    }

    public function insertHoliday(Request $request)
    {
        DB::beginTransaction();
        $create = holiday::create([
            'Name' => $request->input('name'),
            'From_Date' => $request->input('from_date'),
            'To_Date' => $request->input('to_date'),
            'Duration' => $request->input('duration'),
        ]);

        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully inserted',
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


    public function updateHoliday(Request $request, $id)
    {
        DB::beginTransaction();
        $holiday = holiday::findorfail($id);
        $holiday->update([
            'Name' => $request->input('name'),
            'From_Date' => $request->input('from_date'),
            'To_Date' => $request->input('to_date'),
            'Duration' => $request->input('duration'),
        ]);
        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully Updated',
        ];

        DB::commit();
        return response()->json($response);

        DB::rollback();
        $response = [
            'success'   =>  false,
            'message'   =>  'Error while updating',
        ];
        return response()->json($response);
    }

    public function editHoliday($id)
    {
        $holiday = holiday::find($id);
        return response()->json($holiday);
    }

    public function deleteHoliday($id)
    {
        $holiday = holiday::find($id);

        if ($holiday) {
            $holiday->delete();
            $response = [
                'success'   =>  true,
                'message'   =>  'Holiday deleted successfully',
            ];
            return response()->json($response);
        }
    }
}
