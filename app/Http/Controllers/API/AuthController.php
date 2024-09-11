<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\employee;
use App\Models\User;
use App\Models\user_has_page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    public function getUser()
    {
        $user = User::all();
        return response()->json($user);
    }

    public function currentUser()
    {
        $userId = Session::get('User_Id');
        if ($userId) {
            $user = employee::select('employees.Full_Name', 'emp_imgs.img_url')
                ->leftjoin('emp_imgs', 'employees.id', '=', 'emp_imgs.EID')
                ->where('employees.id', '=', $userId)
                ->first();
            return response()->json($user);
        } else {
            return response()->json('logout');
        }
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

        // $input = $request->all();   
        // $input['password']  =  bcrypt($input['password']);
        // $user = User::create($input);

        $user = User::create([
            'name' => $name,
            'email' => $request->input('email'),
            'EID' => $request->input('employee_Id'),
            'password' => bcrypt($request->input('password')),
        ]);

        $success['token']   =   $user->createToken('MyApp')->plainTextToken;
        $success['name']    =   $user->name;
        $response           =   [
            'success'       =>  true,
            'data'          =>  $success,
            'message'       =>  'User register successfully'
        ];
        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
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
                'message'       =>  'Unauthorized'
            ];
            return response()->json($response);
        }
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
