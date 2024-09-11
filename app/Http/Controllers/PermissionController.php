<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\user_has_page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index (){
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function pages()
    {
        $userId = Session::get('User_Id');
        $permissions = user_has_page::select('Page_Id')->where('User_Id', $userId)->get();
        return response()->json($permissions);  
    }

    public function getUser(){
        $user = User::all();
        return response()->json($user);
    }

    public function getRole(){
        $role = Role::all();
        return response()->json($role);
    }
}
