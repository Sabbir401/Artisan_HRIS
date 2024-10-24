<?php

namespace App\Http\Controllers;

use App\Mail\LeaveEmail;
use App\Mail\UserCreated;
use App\Models\employee;
use App\Models\leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendLeaveEmail($id)
    {
        $leave = leave::select('employees.Full_Name', 'employees.Employee_Id', 'leaves.From_Date', 'leaves.To_Date', 'leaves.Purpose', 'leave_types.Name as leave_type', 'officials.Supervisor_Id','designations.Name as designation')
        ->join ('employees', 'employees.id', '=', 'leaves.EID')
        ->join ('leave_types', 'leave_types.id', '=', 'leaves.Leave_Type_Id')
        ->join ('officials', 'officials.EID', '=', 'employees.id')
        ->join ('designations', 'designations.id', '=', 'officials.Designation_Id')
        ->where('leaves.id', $id)
        ->first();

        $supervisor = employee::where('id', $leave->Supervisor_Id)->first();
        $toEmail = 'sabbir@artisanbn.com';
        // $toEmail = $supervisor->Official_Email;
        $name = $leave->Full_Name;
        $eid = $leave->Employee_Id;
        $designation = $leave->designation;
        $from_date = $leave->From_Date;
        $to_date = $leave->To_Date;
        $purpose = $leave->Purpose;
        $leave_type = $leave->leave_type;
        Mail::to($toEmail)->send(new LeaveEmail($from_date, $to_date, $purpose, $leave_type, $name, $eid, $designation));
        return response()->json(['message' => 'Email sent successfully']);
    }

    public function sendUserEmail(Request $request)
    {
        $user = employee::where('id', $request->employee_Id)->first();

        $toEmail = $request->email;
        $name = $user->Full_Name;
        $username = $request->username;
        $password = $request->password;

        Mail::to($toEmail)->send(new UserCreated($name, $username, $password));

        return response()->json(['message' => 'Email sent successfully']);
    }
}
