<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ManageLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ManageLinkController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(Gate::allows('manage_link_access'), 403);
            $dataDepartment = Department::select('departmentName')->where('departmentType','=','ภายในคณะวิทยาศาสตร์')->get();
            if ($request->ajax()) {
                $data = ManageLink::all();
                $result = [];
                $index = 1;

                foreach ($data as $row) {
                    $result[] = [
                        'id' => $row->id,
                        'department_name' => $row->department_name,
                        'link_name' => $row->link_name,
                        'link' => $row->link,
                        'action' => '
                            <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-xs btn-warning btn-sm edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-xs btn-danger btn-sm delete">
                                <i class="fas fa-trash-alt"></i>
                            </a>'
                    ];
                }

                return response()->json(['data' => $result]);
            }

            // ถ้าไม่ใช่ AJAX request ให้แสดงหน้า view ปกติ
            return view('admin.manage_links.index',compact('dataDepartment'));
    }
       

    // public function store(Request $request)
    // {
    //     abort_unless(Gate::allows('manage_link_create'), 403);
    //     // dd($request);
    //     ManageLink::updateOrCreate([
    //                 'id' => $request->department_id
    //             ],
    //             [
    //                     'department_name' => $request->department_name,
    //                     'link_name' => $request->link_name,
    //                     'link' => $request->link,
    //                     'file_name' => $request->fileUpload,
    //             ]);
     
    //     return response()->json(['success'=>'Successfully.']);
    // }

    public function store(Request $request)
    {
        abort_unless(Gate::allows('manage_link_create'), 403);

        $fileName = null;

        if ($request->hasFile('fileUpload') && $request->file('fileUpload')->isValid()) {
            $file = $request->file('fileUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/manage_links', $fileName);
        }

        $data = [
            'department_name' => $request->department_name,
            'link_name'       => $request->link_name,
            'link'            => $request->link,
        ];

        // อัปเดต file_name เฉพาะเมื่อมีไฟล์ใหม่
        if ($fileName) {
            $data['file_name'] = $fileName;
        }

        ManageLink::updateOrCreate(
            ['id' => $request->manage_link_id],
            $data
        );

        return response()->json(['success' => 'Successfully.']);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('manage_link_edit'), 403);
        $data = ManageLink::find($id);
        return response()->json($data);
    }
    

    public function destroy($id)
    {
        abort_unless(Gate::allows('manage_link_delete'), 403);
        ManageLink::find($id)->delete();
      
        return response()->json(['success'=>'Successfully.']);
    }
}
