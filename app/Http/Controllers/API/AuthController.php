<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use App\Models\employee;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use App\Models\user_has_page;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    public function getUser()
    {
        $user = User::all();
        return response()->json($user);
    }

    public function register(Request $request)
    {
        $name = employee::where('id', $request->input('employee_Id'))->pluck('Full_Name')->first();

        $validator = Validator::make($request->all(), [
            'email'     =>  'required|email',
            'password'  =>  'required',
            'c_password' =>  'required|same:password',
        ]);

        if ($validator->fails()) {
            $response = [
                'success'   =>  false,
                'message'   =>  $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $user = User::where('email', $request->input('email'))
            ->orWhere('EID', $request->input('employee_Id'))
            ->first();

        if ($user) {
            // Update the existing user's details
            $user->name = $name;
            $user->password = bcrypt($request->input('password'));
            $user->save();

            $message = 'User updated successfully';
        } else {
            // Create a new user if no existing user is found
            $user = User::create([
                'name' => $name,
                'email' => $request->input('email'),
                'EID' => $request->input('employee_Id'),
                'password' => bcrypt($request->input('password')),
            ]);

            $message = 'User registered successfully';
            
            Mail::to($user->email)->send(new UserCreated($user));
        }

        // Generate token and prepare response data
        $success['token']   =   $user->createToken('MyApp')->plainTextToken;
        $success['name']    =   $user->name;

        // Return success response
        $response = [
            'success'       =>  true,
            'data'          =>  $success,
            'message'       =>  $message
        ];
        return response()->json($response, 200);
    }



    public function changePassword(Request $request)
    {
        $request->validate([
            'password'  =>  'required',
            'confirm_password' =>  'required|same:password',
        ], [
            'password.required' => 'The password is required.',
            'confirm_password.required' => 'The confirm password is required.',
            'confirm_password.same' => 'The password does not match.',
        ]);

        $userId = Session::get('User_Id');

        $user = User::select('users.id',  'users.name', 'users.email', 'users.EID', 'users.password')
            ->join('employees', 'users.EID', '=', 'employees.id')->where('employees.id', $userId)
            ->first();

        if (!$user) {
            $response = [
                'success'   => false,
                'message'   => 'User not found with the provided email address',
            ];
            return response()->json($response, 404);
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        $success['token']   =   $user->createToken('MyApp')->plainTextToken;
        $success['name']    =   $user->name;
        $response           =   [
            'success'       =>  true,
            'data'          =>  $success,
            'message'       =>  'Password Changes Successfully'
        ];
        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'  =>  'required',
            'password'  =>  'required'
        ], [
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
        ]);
        if (Auth::attempt(['email' =>  $request->email, 'password'  =>  $request->password])) {
            $user = Auth::user();

            $user_id = User::where('email', $request->email)->pluck('EID')->first();
            if ($user_id) {
                Session::put('User_Id', $user_id);
            } else {
                Session::put('User_Id', 208);
            }

            // Fetch permissions for the user
            $permissions = user_has_page::where('EID', $user_id)->pluck('Page_Id');
            $decodedPermissions = [];
            foreach ($permissions as $permission) {
                $decodedPermissions = array_merge($decodedPermissions, json_decode($permission, true));
            }

            $success['token']   =   $user->createToken('MyApp')->plainTextToken;
            $success['name']    =   $user->name;
            $success['permissions'] = $decodedPermissions;
            $response       =   [
                'success'       =>  true,
                'data'          =>  $success,
                'message'       =>  'Successfully logged In'
            ];
            return response()->json($response, 200);
        } else {
            $response       =   [
                'success'       =>  false,
                'message'       =>  'Email or password is incorrect'
            ];
            return response()->json($response);
        }
    }


    public function logout()
    {
        Session::flush();
        return response()->json('Your Session is over');
    }



    public function fetchUser($id)
    {
        $user = User::select('users.name', 'users.EID')
            ->join('officials', 'users.EID', '=', 'officials.EID')
            ->where('officials.Department_Id', '=', $id)
            ->get();
        return response()->json($user);
    }
}
