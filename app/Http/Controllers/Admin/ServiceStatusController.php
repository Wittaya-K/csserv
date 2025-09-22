<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceStatusController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(Gate::allows('status_access'), 403);

            if ($request->ajax()) {
                $data = ServiceStatus::all();
                $result = [];
                $index = 1;

                foreach ($data as $row) {
                    $result[] = [
                        'id' => $row->id,
                        'serviceStatusName' => $row->serviceStatusName,
                        'serviceStatus' => $row->serviceStatus,
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
            return view('admin.service_status.index');
    }
       

    public function store(Request $request)
    {
        abort_unless(Gate::allows('status_create'), 403);
        ServiceStatus::updateOrCreate([
                    'id' => $request->service_status_id
                ],
                [
                    'serviceStatusName' => $request->serviceStatusName,
                    'serviceStatus' => $request->serviceStatus,
                ]);        
     
        return response()->json(['success'=>'Successfully.']);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('status_edit'), 403);
        $serviceStatus = ServiceStatus::find($id);
        return response()->json($serviceStatus);
    }
    

    public function destroy($id)
    {
        abort_unless(Gate::allows('status_delete'), 403);
        ServiceStatus::find($id)->delete();
      
        return response()->json(['success'=>'Successfully.']);
    }
}
