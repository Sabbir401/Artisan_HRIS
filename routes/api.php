<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NomineeController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\AttendenceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\AcademicInfoController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\EmployeeAssetController;
use App\Http\Controllers\EmployeeInformationController;
use App\Http\Controllers\TrainingInfoController;
use App\Http\Controllers\WorkExperienceController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/link-storage', function () {
    $targetFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/public/storage';
    symlink($targetFolder, $linkFolder);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});


// Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
//     include('admin/admin.php');
// });


// Route::group(['middleware' => 'auth:sanctum'], function () {

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    //Employee Controller
    Route::get('/employee', [EmployeeController::class, 'index']);
    Route::post('/employee', [EmployeeController::class, 'store']);
    Route::get('/employee/{id}', [EmployeeController::class, 'show']);
    Route::get('/employee/{id}/edit', [EmployeeController::class, 'edit']);
    Route::put('/employee/{id}', [EmployeeController::class, 'update']);

    Route::get('/emp/{id}', [EmployeeController::class, 'find']);
    Route::get('empEmail/{id}', [EmployeeController::class, 'getEmail']);
    Route::post('/employee/{id}', [EmployeeController::class, 'update']);
    Route::get('/allemp', [EmployeeController::class, 'allemp']);
    Route::get('/employee-exl', [EmployeeController::class, 'EmployeeExl']);
    Route::get('/emp-id', [EmployeeController::class, 'EmployeeId']);
    Route::get('/employee-search', [EmployeeController::class, 'search']);


    //Employee Information
    Route::get('/branch', [EmployeeInformationController::class, 'getBranch']);
    Route::get('/blood', [EmployeeInformationController::class, 'getBloodGroup']);
    Route::get('/board', [EmployeeInformationController::class, 'getBoard']);
    Route::get('/company', [EmployeeInformationController::class, 'getCompany']);
    Route::get('/phone', [EmployeeInformationController::class, 'getPhone']);
    Route::get('/religion', [EmployeeInformationController::class, 'getReligion']);
    Route::get('/scale', [EmployeeInformationController::class, 'getScale']);
    Route::get('/education', [EmployeeInformationController::class, 'getEducation']);
    Route::get('/education/{id}', [EmployeeInformationController::class, 'fetchDegree']);
    Route::get('/empType', [EmployeeInformationController::class, 'getEmpType']);
    Route::get('/territory', [EmployeeInformationController::class, 'getTerritory']);
    Route::get('/area', [EmployeeInformationController::class, 'getArea']);
    Route::get('/empimg/{id}', [EmployeeInformationController::class, 'fetchEmployeeImg']);

    //Nominee Controller
    Route::post('/nominee', [NomineeController::class, 'store']);
    Route::get('/nominee/{id}/edit', [NomineeController::class, 'edit']);
    Route::put('/nominee/{id}', [NomineeController::class, 'update']);

    //Child Information
    Route::post('/child', [ChildController::class, 'store']);
    Route::get('/child/{id}', [ChildController::class, 'show']);
    Route::get('/child/{id}/edit', [ChildController::class, 'edit']);
    Route::put('/child/{id}', [ChildController::class, 'update']);


    //Leave controller
    Route::get('/leave', [LeaveController::class, 'index']);
    Route::post('/leave', [LeaveController::class, 'store']);
    Route::get('/leave/{id}', [LeaveController::class, 'show']);
    Route::put('/leave/{id}', [LeaveController::class, 'update']);
    Route::get('/leave-type', [LeaveTypeController::class, 'index']);
    Route::get('/leave-summery/{id}', [LeaveController::class, 'leaveSummery']);
    Route::get('/all-leave', [LeaveController::class, 'allLeave']);

    Route::get('/holiday', [LeaveController::class, 'fetchHoliday']);
    Route::post('/holiday', [LeaveController::class, 'insertHoliday']);
    Route::put('/holiday/{id}', [LeaveController::class, 'updateHoliday']);
    Route::get('/holiday/{id}', [LeaveController::class, 'editHoliday']);
    Route::delete('/delete-holiday/{id}', [LeaveController::class, 'deleteHoliday']);

    //academic controller
    Route::get('/academic', [AcademicInfoController::class, 'index']);
    Route::post('/academic', [AcademicInfoController::class, 'store']);
    Route::get('/academic/{id}', [AcademicInfoController::class, 'show']);
    Route::get('/academic/{id}/edit', [AcademicInfoController::class, 'edit']);
    Route::put('/academic/{id}', [AcademicInfoController::class, 'update']);

    //Training controller
    Route::get('/training', [TrainingInfoController::class, 'index']);
    Route::post('/training', [TrainingInfoController::class, 'store']);
    Route::get('/training/{id}', [TrainingInfoController::class, 'show']);
    Route::get('/training/{id}/edit', [TrainingInfoController::class, 'edit']);
    Route::put('/training/{id}', [TrainingInfoController::class, 'update']);

    //Training controller
    Route::get('/work', [WorkExperienceController::class, 'index']);
    Route::post('/work', [WorkExperienceController::class, 'store']);
    Route::get('/work/{id}', [WorkExperienceController::class, 'show']);
    Route::get('/work/{id}/edit', [WorkExperienceController::class, 'edit']);
    Route::put('/work/{id}', [WorkExperienceController::class, 'update']);

    //Official controller
    Route::post('/official', [OfficialController::class, 'store']);
    Route::get('/official/{id}/edit', [OfficialController::class, 'edit']);
    Route::put('/official/{id}', [OfficialController::class, 'update']);


    Route::get('/department', [DepartmentController::class, 'index']);
    Route::get('/designation', [DesignationController::class, 'index']);
    Route::get('/designation/{id}', [DesignationController::class, 'show']);


    Route::post('/attendence', [AttendenceController::class, 'store']);
    Route::post('/attendence/edit', [AttendenceController::class, 'edit']);
    Route::get('/attendence', [AttendenceController::class, 'getAttendance']);
    Route::get('/location', [AttendenceController::class, 'getTsoLocation']);

    Route::get('/emp-attendence', [AttendenceController::class, 'attendenceEmployee']);
    Route::get('/fetch-attendence', [AttendenceController::class, 'fetchAttendence']);
    Route::get('/fetch-zkt-attendence', [AttendenceController::class, 'fetchZktAttendence']);
    Route::get('/fetch-zkt-user', [AttendenceController::class, 'fetchZktUser']);


    Route::get('/generate-pdf', [AttendenceController::class, 'generatePdf']);
    Route::get('/cv-pdf/{id}', [EmployeeController::class, 'generatePdf']);

    // role and permissions
    Route::get('/permission', [PermissionController::class, 'index']);
    Route::get('/get-user', [PermissionController::class, 'getUser']);
    Route::get('/get-role', [PermissionController::class, 'getRole']);

    // page-permission
    Route::post('/set-permission', [PageController::class, 'store']);
    // Route::get('/get-permission', [PageController::class, 'getPermission']);
    Route::get('/get-user-permission/{id}', [PageController::class, 'getUserPermission']);
    Route::get('/navigation', [NavigationController::class, "getUserNavigation"]);
    Route::get('/getNavigation', [NavigationController::class, "TreeNavigation"]);



    Route::get('/user', [AuthController::class, "getUser"]);
    Route::get('/user/{id}', [AuthController::class, "fetchUser"]);
    Route::get('/current-user', [AuthController::class, "currentUser"]);

    //asset
    Route::get('/get-asset', [AssetController::class, "getAsset"]);
    Route::get('/asset', [AssetController::class, "index"]);
    Route::post('/asset', [AssetController::class, "store"]);
    Route::get('/asset/{id}', [AssetController::class, "editAsset"]);
    Route::put('/asset/{id}', [AssetController::class, "update"]);
    Route::get('/device-type', [AssetController::class, "getDeviceTypes"]);
    Route::delete('/delete-asset/{id}', [AssetController::class, 'deleteAsset']);

    Route::get('/allAsset', [EmployeeAssetController::class, "allAsset"]);
    Route::post('/employee-asset', [EmployeeAssetController::class, "store"]);
    Route::get('/employee-asset', [EmployeeAssetController::class, "index"]);
    Route::get('/editEmpAsset/{id}', [EmployeeAssetController::class, "editEmpAsset"]);
    Route::put('/empAsset/{id}', [EmployeeAssetController::class, "update"]);



    Route::get('/supervisor', [EmployeeInformationController::class, "supervisorTree"]);
});
