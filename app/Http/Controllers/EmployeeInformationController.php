<?php

namespace App\Http\Controllers;

use App\Models\area;
use App\Models\board;
use App\Models\scale;
use App\Models\branch;
use App\Models\degree;
use App\Models\company;
use App\Models\country;
use App\Models\religion;
use App\Models\blood_group;
use App\Models\department;
use App\Models\emp_img;
use App\Models\employee;
use App\Models\employee_type;
use App\Models\leave;
use App\Models\official;
use App\Models\territory;
use Illuminate\Support\Facades\DB;

class EmployeeInformationController extends Controller
{
    public function getBloodGroup()
    {
        $blood = blood_group::all();
        return response()->json($blood);
    }

    public function getBoard()
    {
        $board = board::all();
        return response()->json($board);
    }

    public function getBranch()
    {
        $branch = branch::all();
        return response()->json($branch);
    }

    public function getCompany()
    {
        $company = company::all();
        return response()->json($company);
    }

    public function getPhone()
    {
        $phone = country::all();
        return response()->json($phone);
    }

    public function getDegree()
    {
        $degree = degree::all();
        return response()->json($degree);
    }

    public function getReligion()
    {
        $religion = religion::all();
        return response()->json($religion);
    }

    public function getScale()
    {
        $scale = scale::all();
        return response()->json($scale);
    }

    public function getArea()
    {
        $area = area::all();
        return response()->json($area);
    }

    public function getTerritory()
    {
        $territory = territory::all();
        return response()->json($territory);
    }

    public function getEmpType()
    {
        $emptype = employee_type::all();
        return response()->json($emptype);
    }

    public function fetchDegree($id)
    {
        $degree = DB::table('level_of_educations')
            ->select('id', 'Name')
            ->where('education_id', $id)
            ->orderBy('Name', 'asc')
            ->get();

        return response()->json($degree);
    }

    public function fetchEmployeeImg($id)
    {
        // $image = emp_img::where('EID', $id)->value('img_url');
        $image = emp_img::where('EID', $id)->first(['img_url']);
        return response()->json($image);
    }

    public function getEducation()
    {
        $education = DB::table('level_of_educations')
            ->select('id', 'Name')
            ->whereNull('education_id')
            ->orWhere('education_id', '')
            ->get();

        $degree = DB::table('level_of_educations')
            ->select('id', 'Name')
            ->whereNotNull('education_id')
            ->get();

        return response()->json([
            'education' => $education,
            'degree' => $degree,
        ]);
    }



    function buildTree(array $elements, $parentId = null)
    {
        $tree = [];
        foreach ($elements as $element) {
            if ($element['Supervisor_Id'] == $parentId) {
                $children = $this->buildTree($elements, $element['EID']);
                if ($children) {
                    $element['children'] = $children;
                }
                $tree[] = $element;
            }
        }
        return $tree;
    }

    public function supervisorTree()
    {
        // Fetch all rows from the official table
        $officials = official::get()->toArray();

        // Build the tree starting from the root supervisor (head of the company)
        $tree = $this->buildTree($officials, 0); // Assuming 1 is the root EID (head of the company)

        return ["tree" => $tree];
    }



    public function dashCount()
    {
        $department = department::count();
        $employee =  employee::count();
        $leave = leave::count();

        return [
            'department' => $department,
            'employee' => $employee,
            'leave' => $leave,
        ];
    }
}
