<?php

namespace App\Http\Controllers;

use App\Mail\LeaveEmail;
use App\Mail\UserCreated;
use App\Models\employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendLeaveEmail()
    {
        $toEmail = 'ahmedsabbir3343@gmail.com';
        $subject = 'Leave Application';
        $message = 'Test Mail';

        Mail::to($toEmail)->send(new LeaveEmail($message, $subject));

        return response()->json(['message' => 'Email sent successfully']);
    }

    public function sendUserEmail(Request $request)
    {
        $user = employee::where('id', $request->employee_Id)->first();

        $toEmail = 'ahmedsabbir3343@gmail.com';
        $name = $user->Full_Name;
        $username = $request->username;
        $password = $request->password;

        Mail::to($toEmail)->send(new UserCreated($name, $username, $password));

        return response()->json(['message' => 'Email sent successfully']);
    }
}
