<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceAssign;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ServiceAssignController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Gate::allows('service_assign_access'), 403);

        $tbl_departments = Department::orderBy('id')->get(); //ดึงข้อมูลจากตาราง tbl_departments มาแสดง

            if ($request->ajax()) {
                $data = ServiceAssign::orderBy('id')->where('serviceProvider','=',Auth::user()->username)->get();
                $result = [];
                $index = 1;

                foreach ($data as $row) {
                    $result[] = [
                        'id' => $row->id,
                        'serviceName' => $row->serviceName,
                        'serviceProvider' => $row->serviceProvider,
                        'action' => '
                            <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-xs btn-warning btn-sm editServiceRequest">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-xs btn-danger btn-sm deleteServiceRequest">
                                <i class="fas fa-trash-alt"></i>
                            </a>'
                    ];
                }

                return response()->json(['data' => $result]);
            }

            // ถ้าไม่ใช่ AJAX request ให้แสดงหน้า view ปกติ
            return view('admin.service_assigns.index',compact('tbl_departments'));
    }
       
    public function store(Request $request)
    {
        abort_unless(Gate::allows('service_assign_create'), 403);

        $validator = Validator::make($request->all(), [
			'serviceName' => 'required',
		]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'error' => $validator->errors() ]);
        }else{
            $schedule = ServiceAssign::updateOrCreate(
                [
                    'id' => $request->input('service_assign_id'),
                ],
                [
                'serviceName' => $request->serviceName,
                'serviceProvider' => $request->serviceProvider,
            ]);
            if($schedule){
                return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
            }
        }
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('service_assign_edit'), 403);
        $service_request = ServiceAssign::find($id);
        return response()->json($service_request);
    }
    
    public function find($id){
		$schedule = ServiceAssign::findOrFail($id);
		return response()->json(['status' => true, 'data' => $schedule ]);
	}

    public function destroy($id)
    {
        abort_unless(Gate::allows('service_assign_delete'), 403);
        ServiceAssign::find($id)->delete();
      
        return response()->json(['success'=>'Successfully.']);
    }
}
