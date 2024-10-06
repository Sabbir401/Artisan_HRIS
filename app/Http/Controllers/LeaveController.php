<?php

namespace App\Http\Controllers;

use App\Models\leave;
use App\Models\holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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

    
    public function getAllLeave()
    {
        $subordinates = leave::select('leaves.id', 'employees.id as EID', 'employees.Full_Name', 'employees.Employee_Id', 'leaves.From_Date', 'leaves.To_Date', 'leaves.Status', 'leaves.Attachment_Url', 'leaves.Purpose', 'leave_types.Name as Leave_Type', 'leave_types.id as leave_id', 'designations.Name as designation', 'departments.Name as department')
            ->join('employees', 'leaves.EID', '=', 'employees.id')
            ->join('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
            ->join('officials', 'officials.EID', '=', 'employees.id')
            ->join('departments', 'officials.Department_Id', '=', 'departments.id')
            ->join('designations', 'officials.Designation_Id', '=', 'designations.id')
            ->where('officials.Status', '=', 'N')
            ->orderby('leaves.id', 'desc')
            ->get();

        return $subordinates;
    }


    public function getSubordinates($userId)
    {
        $subordinates = [];
        $queue = [$userId];

        while (!empty($queue)) {
            $currentUserId = array_shift($queue);
            $subordinateleave = leave::select('leaves.id', 'employees.id as EID', 'employees.Full_Name', 'employees.Employee_Id', 'leaves.From_Date', 'leaves.To_Date', 'leaves.Status', 'leaves.Attachment_Url', 'leaves.Purpose', 'leave_types.Name as Leave_Type', 'leave_types.id as leave_id', 'designations.Name as designation', 'departments.Name as department')
                ->leftjoin('employees', 'leaves.EID', '=', 'employees.id')
                ->leftjoin('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
                ->leftjoin('officials', 'officials.EID', '=', 'employees.id')
                ->leftjoin('departments', 'officials.Department_Id', '=', 'departments.id')
                ->leftjoin('designations', 'officials.Designation_Id', '=', 'designations.id')
                ->where('officials.Supervisor_Id', '=', $currentUserId)
                ->where('officials.Status', '=', 'N')
                ->orderby('leaves.id', 'desc')
                ->get();

            foreach ($subordinateleave as $subordinate) {
                $subordinates[] = $subordinate;
                $queue[] = $subordinate->EID;
            }
        }

        return $subordinates;
    }


    public function allLeave()
    {
        $userId = Auth::user()->EID;

        if ($userId) {
            if ($userId === 13 || $userId === 1) {
                $leaves = $this->getAllLeave();
            } else {
                $leaves = $this->getSubordinates($userId);
            }

            foreach ($leaves as $leave) {
                $startDate = new \DateTime($leave->From_Date);
                $endDate = new \DateTime($leave->To_Date);

                $interval = $startDate->diff($endDate);
                $leave->daysBetween = $interval->days + 1;
            }

            return response()->json($leaves);
        } else {
            return response()->json('User not found');
        }
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
        $userId = Session::get('User_Id');
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
                'created_by' => $userId,
                'updated_by' => 0,
            ]);
        } else {
            $leave = leave::create([
                'EID' => $request->input('Employee_Id'),
                'Leave_Type_Id' => $request->input('Leave_Type_Id'),
                'From_Date' => $request->input('From_Date'),
                'To_Date' => $request->input('To_Date'),
                'Purpose' => $request->input('Purpose'),
                'Status' => $request->input('Status'),
                'created_by' => $userId,
                'updated_by' => 0,
            ]);
        }


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
        $leaves = leave::select('employees.id', 'employees.Full_Name', 'emp_imgs.img_url', 'employees.Employee_Id', 'leaves.From_Date', 'leaves.To_Date', 'leaves.Status', 'leaves.Attachment_Url', 'leave_types.Name', 'designations.Name as designation', 'departments.Name as department', 'leave_types.Name as leave_type', 'leave_types.Max_Days')
            ->join('employees', 'leaves.EID', '=', 'employees.id')
            ->join('emp_imgs', 'emp_imgs.EID', '=', 'employees.id')
            ->join('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
            ->join('officials', 'officials.EID', '=', 'employees.id')
            ->join('departments', 'officials.Department_Id', '=', 'departments.id')
            ->join('designations', 'officials.Designation_Id', '=', 'designations.id')
            ->where('officials.Status', '=', 'N')
            ->where('leaves.EID', '=', $id)
            ->orderby('leaves.id', 'desc')
            ->get();

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
        $userId = Auth::user()->EID;
        $leave->update([
            'Leave_Type_Id' => $request->input('Leave_Type'),
            'Status' => $request->input('Status'),
            'From_Date' => $request->input('From_Date'),
            'To_Date' => $request->input('To_Date'),
            'updated_by' => $userId,
        ]);
        $response = [
            'success'   =>  true,
            'message'   =>  'Successfully Updated',
        ];
        return response()->json($response);
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
