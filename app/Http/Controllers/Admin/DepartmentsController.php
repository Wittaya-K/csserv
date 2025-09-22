<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepartmentsController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(Gate::allows('department_access'), 403);

            if ($request->ajax()) {
                $data = Department::all();
                $result = [];
                $index = 1;

                foreach ($data as $row) {
                    $result[] = [
                        'id' => $row->id,
                        'departmentName' => $row->departmentName,
                        'departmentType' => $row->departmentType,
                        'action' => '
                            <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-xs btn-warning btn-sm editDepartment">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-xs btn-danger btn-sm deleteDepartment">
                                <i class="fas fa-trash-alt"></i>
                            </a>'
                    ];
                }

                return response()->json(['data' => $result]);
            }

            // ถ้าไม่ใช่ AJAX request ให้แสดงหน้า view ปกติ
            return view('admin.departments.index');
    }
       

    public function store(Request $request)
    {
        abort_unless(Gate::allows('department_create'), 403);
        Department::updateOrCreate([
                    'id' => $request->department_id
                ],
                [
                    'departmentName' => $request->departmentName,
                    'departmentType' => $request->departmentType,
                ]);        
     
        return response()->json(['success'=>'Department saved successfully.']);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('department_edit'), 403);
        $department = Department::find($id);
        return response()->json($department);
    }
    

    public function destroy($id)
    {
        abort_unless(Gate::allows('department_delete'), 403);
        Department::find($id)->delete();
      
        return response()->json(['success'=>'Department deleted successfully.']);
    }
}
