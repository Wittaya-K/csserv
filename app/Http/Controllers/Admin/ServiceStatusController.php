<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ServiceStatusController extends Controller
{

    // public function index(Request $request)
    // {
    //     abort_unless(Gate::allows('status_access'), 403);
    //         if ($request->ajax()) {
    //             $data = ServiceStatus::all();
    //             $result = [];
    //             $index = 1;

    //             foreach ($data as $row) {
    //                 $result[] = [
    //                     'id' => $row->id,
    //                     'serviceStatusName' => $row->serviceStatusName,
    //                     // 'serviceStatus' => $row->serviceStatus,
    //                     'action' => '
    //                         <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-xs btn-warning btn-sm editDepartment">
    //                             <i class="fas fa-edit"></i>
    //                         </a>
    //                         <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-xs btn-danger btn-sm deleteDepartment">
    //                             <i class="fas fa-trash-alt"></i>
    //                         </a>'
    //                 ];
    //             }

    //             return response()->json(['data' => $result]);
    //         }

    //         // ถ้าไม่ใช่ AJAX request ให้แสดงหน้า view ปกติ
    //         return view('admin.service_status.index');
    // }
       

    // public function store(Request $request)
    // {
    //     abort_unless(Gate::allows('status_create'), 403);
    //     ServiceStatus::updateOrCreate([
    //                 'id' => $request->service_status_id
    //             ],
    //             [
    //                 'serviceStatusName' => $request->serviceStatusName,
    //                 // 'serviceStatus' => $request->serviceStatus,
    //             ]);        
     
    //     return response()->json(['success'=>'Successfully.']);
    // }

    // public function edit($id)
    // {
    //     abort_unless(Gate::allows('status_edit'), 403);
    //     $serviceStatus = ServiceStatus::find($id);
    //     return response()->json($serviceStatus);
    // }
    

    // public function destroy($id)
    // {
    //     abort_unless(Gate::allows('status_delete'), 403);
    //     ServiceStatus::find($id)->delete();
      
    //     return response()->json(['success'=>'Successfully.']);
    // }

    // ============================================================
    // หมายเหตุ: ServiceStatus เป็น Admin-only reference data
    // (สถานะงาน เช่น "รอดำเนินการ", "เสร็จสิ้น")
    // ไม่มี per-user ownership — ป้องกันด้วย Role/Permission แทน
    //
    // LAYER 1: Authentication — middleware('auth') ใน web.php ✅
    // LAYER 2: Gate::allows  — permission check ทุก method ✅
    // LAYER 3: Ownership     — N/A (Admin-only global data)
    // LAYER 4: Input/Output  — validate + only() ✅
    // ============================================================
 
    /**
     * แสดงรายการ ServiceStatus ทั้งหมด (Admin เท่านั้น)
     * LAYER 4: ส่งเฉพาะ field ที่จำเป็น
     */
    public function index(Request $request)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('status_access'), 403);
 
        if ($request->ajax()) {
            $data = ServiceStatus::orderBy('id')->get();
 
            // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
            $result = $data->map(fn($row) => [
                'id'                => $row->id,
                'serviceStatusName' => $row->serviceStatusName,
                'action'            => '
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
 
        return view('admin.service_status.index');
    }
 
    /**
     * บันทึก / อัปเดต ServiceStatus
     * LAYER 2: แยก Gate create vs edit
     * LAYER 4: Validate input + only field ที่อนุญาต
     */
    public function store(Request $request)
    {
        $serviceStatusId = $request->input('service_status_id');
 
        // LAYER 2 — Permission: แยก create vs edit
        if ($serviceStatusId) {
            abort_unless(Gate::allows('status_edit'), 403);
        } else {
            abort_unless(Gate::allows('status_create'), 403);
        }
 
        // LAYER 4 — Validate: กำหนด type และขอบเขตของ input
        $validator = Validator::make($request->all(), [
            'serviceStatusName' => 'required|string|max:255',
            'service_status_id' => 'nullable|integer|exists:service_status,id',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors(),
            ], 422);
        }
 
        // LAYER 4 — only() field ที่อนุญาต ป้องกัน mass assignment
        ServiceStatus::updateOrCreate(
            ['id' => $serviceStatusId],
            $request->only(['serviceStatusName'])
        );
 
        return response()->json(['success' => 'Successfully.']);
    }
 
    /**
     * ดึงข้อมูลสำหรับ edit form
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ edit
     * LAYER 4: firstOrFail() + only() — ไม่ส่ง field ลับ
     */
    public function edit($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('status_edit'), 403);
 
        // LAYER 4 — findOrFail แทน find เพื่อป้องกัน null → 500
        //           ได้ 404 แทนถ้า record ไม่มี
        $serviceStatus = ServiceStatus::findOrFail($id);
 
        // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
        return response()->json(
            $serviceStatus->only(['id', 'serviceStatusName'])
        );
    }
 
    /**
     * ลบ ServiceStatus
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ delete
     * LAYER 4: findOrFail() — ป้องกัน null pointer → 500
     */
    public function destroy($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('status_delete'), 403);
 
        // LAYER 4 — findOrFail แทน find()->delete()
        //           ป้องกัน null pointer error (500 → 404)
        $serviceStatus = ServiceStatus::findOrFail($id);
        $serviceStatus->delete();
 
        return response()->json(['success' => 'Successfully.']);
    }
}
