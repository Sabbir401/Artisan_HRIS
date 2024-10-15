<?php

namespace App\Http\Controllers;

use App\Models\leave;
use App\Models\holiday;
use App\Models\employee;
use App\Models\religion;
use App\Models\attendence;
use Illuminate\Http\Request;
use App\Models\zkt_attendence;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class AttendenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $zk;

    //Manual Attendence Save
    public function index()
    {
        $attendence = attendence::get();
        return response()->json($attendence);
    }

    public function getAttendance(Request $request)
    {
        $month = $request->query('month');
        $attendance = attendence::whereMonth('date', $month + 1)->get();
        return response()->json($attendance);
    }

    public function fetchZktAttendence()
    {
        Artisan::call('attendance:fetch');
        return response()->json(['message' => 'Attendance fetched successfully.']);
    }

    public function fetchZktUser()
    {
        Artisan::call('zktuser:fetch');
        return response()->json(['message' => 'User fetched successfully.']);
    }

    public function getTsoLocation()
    {
        $date = Carbon::now()->toDateString();

        $location = attendence::select('employees.Employee_Id', 'employees.Full_Name', 'attendences.latitude', 'attendences.longitude')
            ->join('employees', 'attendences.EID', '=', 'employees.id')
            ->where('Date', $date)
            ->get();
        return response()->json($location);
    }

    public function store(Request $request)
    {
        $userId = Auth::user()->EID;
        $attendanceData = $request->input('attendanceData');
        $year = now()->year;
        $month = $request->input('month');

        foreach ($attendanceData as $employeeData) {
            $employeeId = $employeeData['id'];
            $attendance = $employeeData['attendance'];

            foreach ($attendance as $day => $status) {
                if (!empty($status)) {
                    attendence::updateOrCreate(
                        [
                            'EID' => $employeeId,
                            'Date' => Carbon::create($year, $month + 1, $day + 1)->toDateString(),
                        ],
                        [
                            'Status' => $status,
                            'created_by' => $userId,
                        ]
                    );
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function generatePdf()
    {
        $attendances = religion::get();
        $employee = employee::get();
        $pdf = Pdf::loadView('reports.attendance', compact('employee'));

        return $pdf->stream('attendance_report.pdf');
    }

    // public function getSubordinates($userId)
    // {
    //     $subordinates = employee::select('employees.id', 'employees.Full_Name', 'employees.Employee_Id', 'designations.Name as designation', 'departments.Name as department', 'branches.Name as branch')
    //         ->leftjoin('officials', 'officials.EID', '=', 'employees.id')
    //         ->leftjoin('departments', 'officials.Department_Id', '=', 'departments.id')
    //         ->leftjoin('designations', 'officials.Designation_Id', '=', 'designations.id')
    //         ->leftjoin('branches', 'officials.Job_Location_Id', '=', 'branches.id')
    //         ->where('officials.Supervisor_Id', '=', $userId)
    //         ->where('officials.Status', '=', 'N')
    //         ->orderby('departments.Name', 'asc')
    //         ->get();

    //     $allSubordinates = [];

    //     foreach ($subordinates as $subordinate) {
    //         $allSubordinates[] = $subordinate;
    //         $allSubordinates = array_merge($allSubordinates, $this->getSubordinates($subordinate->id));
    //     }

    //     return $allSubordinates;
    // }

    public function getSubordinates($userId)
    {
        $subordinates = [];
        $queue = [$userId];

        while (!empty($queue)) {
            $currentUserId = array_shift($queue);
            $subordinateEmployees = employee::select('employees.id', 'employees.Full_Name', 'employees.Employee_Id', 'designations.Name as designation', 'departments.Name as department', 'branches.Name as branch')
                ->leftjoin('officials', 'officials.EID', '=', 'employees.id')
                ->leftjoin('departments', 'officials.Department_Id', '=', 'departments.id')
                ->leftjoin('designations', 'officials.Designation_Id', '=', 'designations.id')
                ->leftjoin('branches', 'officials.Job_Location_Id', '=', 'branches.id')
                ->where('officials.Supervisor_Id', '=', $currentUserId)
                ->where('officials.Status', '=', 'N')
                ->orderby('departments.Name', 'asc')
                ->get();

            foreach ($subordinateEmployees as $subordinate) {
                $subordinates[] = $subordinate;
                $queue[] = $subordinate->id;
            }
        }

        return $subordinates;
    }


    public function attendenceEmployee()
    {
        $userId = Auth::user()->EID;
        if ($userId === 13 || $userId === 1) {
            $userId = 208;
        }

        if ($userId) {
            $employee = $this->getSubordinates($userId);

            return response()->json($employee);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    //The optimize code

    // public function fetchAttendence()
    // {
    //     // Fetch all holidays
    //     $holidays = holiday::all();

    //     // Fetch all approved leaves with leave types
    //     $leaves = leave::select('leaves.EID', 'leaves.From_Date', 'leaves.To_Date', 'leave_types.Name')
    //         ->join('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
    //         ->where('leaves.Status', '=', 'Approved')
    //         ->get();

    //     // Fetch all attendance records and group by User_id and date
    //     $attendances = zkt_attendence::all()->groupBy(['User_id', 'date']);

    //     // Determine the minimum and maximum dates from the attendance data
    //     $minDate = zkt_attendence::min('date');
    //     $maxDate = min(zkt_attendence::max('date'), date('Y-m-d')); // Stop at the current date

    //     // Iterate through each user
    //     foreach ($attendances as $userId => $userAttendances) {
    //         // Loop through each date from minimum to maximum
    //         $currentDate = $minDate;
    //         while ($currentDate <= $maxDate) {
    //             // Format the date to YYYY-MM-DD
    //             $date = $currentDate;

    //             // Check if there is any attendance record for the user on this date
    //             if (isset($userAttendances[$date])) {
    //                 $attendanceRecords = $userAttendances[$date];

    //                 // Extract the first punch-in time and last punch-out time for each user each day
    //                 $timeIn = $attendanceRecords->sortBy('time')->first()->time;
    //                 $timeOut = $attendanceRecords->sortByDesc('time')->first()->time;

    //                 // Determine status based on the first punch-in time
    //                 $status = 'A'; // Default status is Absent
    //                 if ($timeIn) {
    //                     $status = ($timeIn > '11:00:00') ? 'L' : 'P';
    //                 }
    //             } else {
    //                 // Check if the date falls within any leave period
    //                 $isOnLeave = $leaves->first(function ($leave) use ($userId, $date) {
    //                     return $leave->EID == $userId && $date >= $leave->From_Date && $date <= $leave->To_Date;
    //                 });

    //                 if ($isOnLeave) {
    //                     $status = $isOnLeave->Name; // Set status to leave type
    //                     $timeIn = $timeOut = null;
    //                 } else {
    //                     // Check if the date falls within any holiday period
    //                     $isHoliday = $holidays->contains(function ($holiday) use ($date) {
    //                         return $date >= $holiday->From_Date && $date <= $holiday->To_Date;
    //                     });

    //                     if ($isHoliday) {
    //                         $status = 'H';
    //                         $timeIn = $timeOut = null;
    //                     } else {
    //                         $status = 'A';
    //                         $timeIn = $timeOut = null;
    //                     }
    //                 }
    //             }

    //             // Update or create the attendance record in the attendence table
    //             attendence::updateOrCreate(
    //                 [
    //                     'EID' => $userId,
    //                     'Date' => $date,
    //                 ],
    //                 [
    //                     'Time_In' => $timeIn,
    //                     'Time_Out' => $timeOut,
    //                     'Status' => $status,
    //                 ]
    //             );

    //             // Move to the next day
    //             $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    //         }
    //     }

    //     return response()->json(['message' => 'Attendance records processed successfully']);
    // }




    public function edit(Request $request)
    {
        $EID = $request->input('Employee_Id');
        $startDate = $request->input('From_Date');
        $endDate = $request->input('To_Date');

        $attendances = attendence::select('employees.full_name', 'attendences.Date', 'attendences.Time_In', 'attendences.Time_Out', 'attendences.Status')
            ->join('employees', 'employees.id', '=', 'attendences.EID')
            ->whereBetween('attendences.Date', [$startDate, $endDate])
            ->where('attendences.EID', $EID)
            ->get();

        return response()->json($attendances);
    }


    public function fetchAttendence()
    {
        // Fetch all holidays, leaves, and attendance records as before
        $holidays = holiday::all();
        $leaves = leave::select('leaves.EID', 'leaves.From_Date', 'leaves.To_Date', 'leave_types.short_name')
            ->join('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
            ->where('leaves.Status', '=', 'Approved')
            ->get();
    
        $attendances = zkt_attendence::all()->groupBy(['User_id', 'date']);
    
        // Get the last processed date from the attendences table
        $lastProcessedDate = attendence::max('Date');
    
        // Set the start date to the last processed date or a default date
        $startDate = $lastProcessedDate 
            ? Carbon::parse($lastProcessedDate)->startOfMonth()->addMonth()->format('Y-m-d') 
            : '2023-10-30';  // Default start date if no previous record exists
    
        // Set the end date as the current date
        $endDate = Carbon::now()->format('Y-m-d');
    
        // Initialize the data array
        $insertData = [];
    
        foreach ($attendances as $userId => $userAttendances) {
            $currentDate = $startDate;
    
            // Iterate through each date from start to end date
            while ($currentDate <= $endDate) {
                // Check for attendance on the current date
                if (isset($userAttendances[$currentDate])) {
                    $attendanceRecords = $userAttendances[$currentDate];
    
                    $timeIn = $attendanceRecords->sortBy('time')->first()->time;
                    $timeOut = $attendanceRecords->sortByDesc('time')->first()->time;
                    $status = ($timeIn > '11:00:00') ? 'L' : 'P'; // Determine status (Late or Present)
    
                } else {
                    $status = $this->determineStatus($userId, $currentDate, $holidays, $leaves);
                    $timeIn = $timeOut = null;
                }
    
                // Add record to the insert array
                $insertData[] = [
                    'EID' => $userId,
                    'Date' => $currentDate,
                    'Time_In' => $timeIn,
                    'Time_Out' => $timeOut,
                    'Status' => $status,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
    
                // If the batch size exceeds 1000, insert the data and reset the array
                if (count($insertData) >= 1000) {
                    DB::table('attendences')->insert($insertData);
                    $insertData = []; // Reset insertData array after each batch insert
                }
    
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }
    
        // Insert any remaining data after loop
        if (!empty($insertData)) {
            DB::table('attendences')->insert($insertData);
        }
    
        return response()->json(['message' => 'Attendance records processed successfully']);
    }
    
    /**
     * Determine the status of an employee based on leave, holiday, and weekends.
     */
    private function determineStatus($userId, $date, $holidays, $leaves)
    {
        // Check if the date falls within any leave period
        $isOnLeave = $leaves->first(function ($leave) use ($userId, $date) {
            return $leave->EID == $userId && $date >= $leave->From_Date && $date <= $leave->To_Date;
        });
        
        if ($isOnLeave) {
            return $isOnLeave->short_name; // Set status to leave type short name
        }
    
        // Check if the date falls within any holiday period
        $isHoliday = $holidays->contains(function ($holiday) use ($date) {
            return $date >= $holiday->From_Date && $date <= $holiday->To_Date;
        });
    
        if ($isHoliday) {
            return 'H'; // Holiday
        }
    
        // Check if the date is a Friday (adjust according to your weekend setting)
        if (Carbon::parse($date)->isFriday()) {
            return 'F'; // Friday/Weekend
        }
        return 'A';
    
    }




}
