<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{

    // ============================================================
    // หมายเหตุ: Department เป็น Admin-only reference data
    // (สถานะงาน เช่น "รอดำเนินการ", "เสร็จสิ้น")
    // ไม่มี per-user ownership — ป้องกันด้วย Role/Permission แทน
    //
    // LAYER 1: Authentication — middleware('auth') ใน web.php ✅
    // LAYER 2: Gate::allows  — permission check ทุก method ✅
    // LAYER 3: Ownership     — N/A (Admin-only global data)
    // LAYER 4: Input/Output  — validate + only() ✅
    // ============================================================
 
    /**
     * แสดงรายการ Department ทั้งหมด (Admin เท่านั้น)
     * LAYER 4: ส่งเฉพาะ field ที่จำเป็น
     */
    public function index(Request $request)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('department_access'), 403);

        if ($request->ajax()) {
            $data = Department::orderBy('id')->get();

            // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
            $result = $data->map(fn($row) => [
                'id'             => $row->id,
                'departmentName' => $row->departmentName,
                'departmentType' => $row->departmentType,
                'action'         => '
                    <a href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-id="' . $row->id . '"
                    data-original-title="Edit"
                    class="edit btn btn-xs btn-warning btn-sm editDepartment">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-id="' . $row->id . '"
                    data-original-title="Delete"
                    class="btn btn-xs btn-danger btn-sm deleteDepartment">
                        <i class="fas fa-trash-alt"></i>
                    </a>',
            ]);

            return response()->json(['data' => $result]);
        }

        return view('admin.departments.index');
    }
       

    public function store(Request $request)
    {     
        $departmentId = $request->input('department_id');
 
        // LAYER 2 — Permission: แยก create vs edit
        if ($departmentId) {
            abort_unless(Gate::allows('department_edit'), 403);
        } else {
            abort_unless(Gate::allows('department_create'), 403);
        }
 
        // LAYER 4 — Validate: กำหนด type และขอบเขตของ input
        $validator = Validator::make($request->all(), [
            'departmentName' => 'required|string|max:255',
            'departmentType' => 'required|string|max:255',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors(),
            ], 422);
        }
 
        // LAYER 4 — only() field ที่อนุญาต ป้องกัน mass assignment
        Department::updateOrCreate(
            ['id' => $departmentId],
            $request->only(['departmentName','departmentType'])
        );

        return response()->json(['success'=>'Department saved successfully.']);
    }

    public function edit($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('department_edit'), 403);
 
        // LAYER 4 — findOrFail แทน find เพื่อป้องกัน null → 500
        //           ได้ 404 แทนถ้า record ไม่มี
        $department = Department::findOrFail($id);
 
        // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
        return response()->json(
            $department->only(['id', 'departmentName','departmentType'])
        );
    }
    

    public function destroy($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('department_delete'), 403);
 
        // LAYER 4 — findOrFail แทน find()->delete()
        //           ป้องกัน null pointer error (500 → 404)
        $department = Department::findOrFail($id);
        $department->delete();
 
        return response()->json(['success' => 'Successfully.']);
    }
}
