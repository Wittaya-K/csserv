<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PriorityController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(Gate::allows('priority_access'), 403);

            if ($request->ajax()) {
                $data = Priority::all();
                $result = [];
                $index = 1;

                foreach ($data as $row) {
                    $result[] = [
                        'id' => $row->id,
                        'priorityName' => $row->priorityName,
                        'priorityStatus' => $row->priorityStatus,
                        // 'action' => '
                        //     <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-xs btn-warning btn-sm editDepartment">
                        //         <i class="fas fa-edit"></i>
                        //     </a>
                        //     <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-xs btn-danger btn-sm deleteDepartment">
                        //         <i class="fas fa-trash-alt"></i>
                        //     </a>'
                    ];
                }

                return response()->json(['data' => $result]);
            }

            // ถ้าไม่ใช่ AJAX request ให้แสดงหน้า view ปกติ
            return view('admin.prioritys.index');
    }
       

    public function store(Request $request)
    {
        abort_unless(Gate::allows('priority_create'), 403);
        Priority::updateOrCreate([
                    'id' => $request->priority_id
                ],
                [
                    'priorityName' => $request->priorityName,
                    'priorityStatus' => $request->priorityStatus,
                ]);        
     
        return response()->json(['success'=>'Successfully.']);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('priority_edit'), 403);
        $priority = Priority::find($id);
        return response()->json($priority);
    }
    

    public function destroy($id)
    {
        abort_unless(Gate::allows('priority_delete'), 403);
        Priority::find($id)->delete();
      
        return response()->json(['success'=>'Successfully.']);
    }
}
