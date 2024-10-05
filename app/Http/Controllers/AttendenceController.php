<?php

namespace App\Http\Controllers;

use App\Models\employee;
use App\Models\religion;
use App\Models\attendence;
use App\Models\holiday;
use App\Models\leave;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\zkt_attendence;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        $userId = Session::get('User_Id');
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


    public function fetchAttendence()
    {
        // Fetch all holidays
        $holidays = holiday::all();

        // Fetch all approved leaves with leave types
        $leaves = leave::select('leaves.EID', 'leaves.From_Date', 'leaves.To_Date', 'leave_types.Name')
            ->join('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
            ->where('leaves.Status', '=', 'Approved')
            ->get();

        // Fetch all attendance records and group by User_id and date
        $attendances = zkt_attendence::all()->groupBy(['User_id', 'date']);

        // Determine the minimum and maximum dates from the attendance data
        $minDate = zkt_attendence::min('date');
        $maxDate = min(zkt_attendence::max('date'), date('Y-m-d')); // Stop at the current date

        // Iterate through each user
        foreach ($attendances as $userId => $userAttendances) {
            // Loop through each date from minimum to maximum
            $currentDate = $minDate;
            while ($currentDate <= $maxDate) {
                // Format the date to YYYY-MM-DD
                $date = $currentDate;

                // Check if there is any attendance record for the user on this date
                if (isset($userAttendances[$date])) {
                    $attendanceRecords = $userAttendances[$date];

                    // Extract the first punch-in time and last punch-out time for each user each day
                    $timeIn = $attendanceRecords->sortBy('time')->first()->time;
                    $timeOut = $attendanceRecords->sortByDesc('time')->first()->time;

                    // Determine status based on the first punch-in time
                    $status = 'A'; // Default status is Absent
                    if ($timeIn) {
                        $status = ($timeIn > '11:00:00') ? 'L' : 'P';
                    }
                } else {
                    // Check if the date falls within any leave period
                    $isOnLeave = $leaves->first(function ($leave) use ($userId, $date) {
                        return $leave->EID == $userId && $date >= $leave->From_Date && $date <= $leave->To_Date;
                    });

                    if ($isOnLeave) {
                        $status = $isOnLeave->Name; // Set status to leave type
                        $timeIn = $timeOut = null;
                    } else {
                        // Check if the date falls within any holiday period
                        $isHoliday = $holidays->contains(function ($holiday) use ($date) {
                            return $date >= $holiday->From_Date && $date <= $holiday->To_Date;
                        });

                        if ($isHoliday) {
                            $status = 'H';
                            $timeIn = $timeOut = null;
                        } else {
                            $status = 'A';
                            $timeIn = $timeOut = null;
                        }
                    }
                }

                // Update or create the attendance record in the attendence table
                attendence::updateOrCreate(
                    [
                        'EID' => $userId,
                        'Date' => $date,
                    ],
                    [
                        'Time_In' => $timeIn,
                        'Time_Out' => $timeOut,
                        'Status' => $status,
                    ]
                );

                // Move to the next day
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }

        return response()->json(['message' => 'Attendance records processed successfully']);
    }




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


    // public function fetchAttendence()
    // {
    //     // Step 1: Get the latest date processed in the 'attendence' table
    //     $lastProcessedDate = attendence::max('Date') ?? '1970-01-01';

    //     // Step 2: Determine the range of dates to process based on new attendance data
    //     $minDate = zkt_attendence::where('date', '>', $lastProcessedDate)->min('date');
    //     $maxDate = min(zkt_attendence::max('date'), date('Y-m-d')); // Stop at the current date

    //     if (!$minDate || $minDate > $maxDate) {
    //         return response()->json(['message' => 'No new attendance records to process']);
    //     }

    //     // Step 3: Fetch required data only for the date range
    //     $holidays = holiday::where('From_Date', '<=', $maxDate)->where('To_Date', '>=', $minDate)->get();
    //     $leaves = leave::select('leaves.EID', 'leaves.From_Date', 'leaves.To_Date', 'leave_types.Name', 'leave_types.Short_Name')
    //         ->join('leave_types', 'leaves.Leave_Type_Id', '=', 'leave_types.id')
    //         ->where('leaves.Status', '=', 'Approved')
    //         ->where(function ($query) use ($minDate, $maxDate) {
    //             $query->whereBetween('leaves.From_Date', [$minDate, $maxDate])
    //                 ->orWhereBetween('leaves.To_Date', [$minDate, $maxDate])
    //                 ->orWhereRaw('? BETWEEN leaves.From_Date AND leaves.To_Date', [$minDate])
    //                 ->orWhereRaw('? BETWEEN leaves.From_Date AND leaves.To_Date', [$maxDate]);
    //         })
    //         ->get();

    //     $attendances = zkt_attendence::whereBetween('date', [$minDate, $maxDate])->get()->groupBy(['User_id', 'date']);

    //     // Step 4: Process the attendance data for each user and date in the range
    //     foreach ($attendances as $userId => $userAttendances) {
    //         // Track dates already processed for this user
    //         $processedDates = attendence::where('EID', $userId)->pluck('Date')->toArray();

    //         foreach ($userAttendances as $date => $attendanceRecords) {
    //             // Skip if the date is already processed for this user
    //             if (in_array($date, $processedDates)) {
    //                 continue;
    //             }

    //             // Extract the first punch-in time and last punch-out time for each user each day
    //             $timeIn = $attendanceRecords->sortBy('time')->first()->time;
    //             $timeOut = $attendanceRecords->sortByDesc('time')->first()->time;

    //             // Determine day of the week (1 = Monday, ..., 5 = Friday, ..., 7 = Sunday)
    //             $dayOfWeek = date('N', strtotime($date));

    //             // Initial status is set based on the day of the week
    //             $status = $dayOfWeek == 5 ? 'F' : ($timeIn ? (($timeIn > '11:00:00') ? 'L' : 'P') : 'A');

    //             // Check if the date falls within any leave period
    //             $leaveRecord = $leaves->first(function ($leave) use ($userId, $date) {
    //                 return $leave->EID == $userId && $date >= $leave->From_Date && $date <= $leave->To_Date;
    //             });

    //             if ($leaveRecord) {
    //                 $status = $leaveRecord->Short_Name; // Set status to leave type
    //                 $timeIn = $timeOut = null;
    //             } else {
    //                 // Check if the date is a holiday
    //                 $isHoliday = $holidays->contains(function ($holiday) use ($date) {
    //                     return $date >= $holiday->From_Date && $date <= $holiday->To_Date;
    //                 });

    //                 if ($isHoliday) {
    //                     $status = 'H'; // Set status to Holiday
    //                     $timeIn = $timeOut = null;
    //                 }
    //             }

    //             // Prepare data for batch insertion
    //             $data[] = [
    //                 'EID' => $userId,
    //                 'Date' => $date,
    //                 'Time_In' => $timeIn,
    //                 'Time_Out' => $timeOut,
    //                 'Status' => $status,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
    //     }

    //     // Step 5: Bulk insert the processed data for efficiency
    //     if (!empty($data)) {
    //         attendence::insert($data);
    //     }

    //     return response()->json(['message' => 'Attendance records processed successfully']);
    // }
}
