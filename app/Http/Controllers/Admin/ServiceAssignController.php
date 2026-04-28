<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceAssign;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ServiceAssignController extends Controller
{
    // ============================================================
    // LAYER 1: Authentication — ครอบทุก route ผ่าน middleware auth
    //          ใน web.php แล้ว ไม่ต้องทำซ้ำที่นี่
    // ============================================================

    /**
     * แสดงรายการ ServiceAssign เฉพาะของตัวเอง
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ access
     * LAYER 3: scope where username — ownership filter
     * LAYER 4: output เฉพาะ field ที่จำเป็น (ไม่ส่ง field ลับออกไป)
     */
    public function index(Request $request)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('service_assign_access'), 403);

        $tbl_departments = Department::orderBy('id')->get();

        if ($request->ajax()) {
            // LAYER 3 — Ownership: ดึงเฉพาะ record ของตัวเอง
            $data = ServiceAssign::orderBy('id')
                ->where('serviceProvider', '=', Auth::user()->username)
                ->get();

            $result = [];

            foreach ($data as $row) {
                // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
                $result[] = [
                    'id'              => $row->id,
                    'serviceName'     => $row->serviceName,
                    'serviceProvider' => $row->serviceProvider,
                    'action'          => '
                        <a href="javascript:void(0)"
                           data-toggle="tooltip"
                           data-id="' . $row->id . '"
                           data-original-title="Edit"
                           class="edit btn btn-xs btn-warning btn-sm editServiceRequest">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="javascript:void(0)"
                           data-toggle="tooltip"
                           data-id="' . $row->id . '"
                           data-original-title="Delete"
                           class="btn btn-xs btn-danger btn-sm deleteServiceRequest">
                            <i class="fas fa-trash-alt"></i>
                        </a>',
                ];
            }

            return response()->json(['data' => $result]);
        }

        return view('admin.service_assigns.index', compact('tbl_departments'));
    }

    /**
     * บันทึก / อัปเดต ServiceAssign
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ create/edit
     * LAYER 3: Ownership — ถ้า update ต้องเป็น record ของตัวเอง
     * LAYER 4: only() — รับเฉพาะ field ที่อนุญาต, serviceProvider บังคับจาก session
     */
    public function store(Request $request)
    {
        $serviceRequestId = $request->input('service_request_id');

        // LAYER 2 — Permission: แยก create vs edit
        if ($serviceRequestId) {
            abort_unless(Gate::allows('service_assign_edit'), 403);
        } else {
            abort_unless(Gate::allows('service_assign_create'), 403);
        }

        // LAYER 4 — Validate เฉพาะ field ที่อนุญาต
        $validator = Validator::make($request->all(), [
            'serviceName' => 'required|string|max:255',
            // ไม่ validate serviceProvider จาก request
            // เพราะบังคับใช้จาก Auth แทน
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()]);
        }

        // LAYER 3 — Ownership check ก่อน update
        if ($serviceRequestId) {
            $existing = ServiceAssign::where('id', $serviceRequestId)
                ->where('serviceProvider', Auth::user()->username)
                ->first();

            if (!$existing) {
                // ไม่ใช่ record ของตัวเอง หรือ record ไม่มีอยู่
                return response()->json(['status' => false, 'error' => 'Unauthorized'], 403);
            }
        }

        // LAYER 4 — serviceProvider บังคับมาจาก Auth ไม่รับจาก request
        //           ป้องกัน user ปลอม serviceProvider เป็นคนอื่น
        $schedule = ServiceAssign::updateOrCreate(
            [
                'id' => $serviceRequestId,
            ],
            [
                'serviceName'     => $request->input('serviceName'),
                'serviceProvider' => Auth::user()->username, // บังคับจาก session เสมอ
            ]
        );

        if ($schedule) {
            return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
        }

        return response()->json(['status' => false, 'message' => 'เกิดข้อผิดพลาด'], 500);
    }

    /**
     * ดึงข้อมูลสำหรับ edit form
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ edit
     * LAYER 3: Ownership — ต้องเป็น record ของตัวเอง
     * LAYER 4: only() — ส่งเฉพาะ field ที่จำเป็น
     */
    public function edit($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('service_assign_edit'), 403);

        // LAYER 3 — Ownership: ต้องเป็น record ของตัวเอง
        $service_request = ServiceAssign::where('id', $id)
            ->where('serviceProvider', Auth::user()->username)
            ->firstOrFail(); // 404 ถ้าไม่ใช่ของตัวเอง

        // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
        return response()->json(
            $service_request->only(['id', 'serviceName', 'serviceProvider'])
        );
    }

    /**
     * ค้นหา ServiceAssign ตาม id
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ access
     * LAYER 3: Ownership — ต้องเป็น record ของตัวเอง
     * LAYER 4: only() — ส่งเฉพาะ field ที่จำเป็น
     */
    public function find($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('service_assign_access'), 403);

        // LAYER 3 — Ownership: ต้องเป็น record ของตัวเอง
        $schedule = ServiceAssign::where('id', $id)
            ->where('serviceProvider', Auth::user()->username)
            ->firstOrFail(); // 404 ถ้าไม่ใช่ของตัวเอง

        // LAYER 4 — Output filter
        return response()->json([
            'status' => true,
            'data'   => $schedule->only(['id', 'serviceName', 'serviceProvider']),
        ]);
    }

    /**
     * ลบ ServiceAssign
     * LAYER 2: Gate::allows — ตรวจสิทธิ์ delete
     * LAYER 3: Ownership — ต้องเป็น record ของตัวเอง
     */
    public function destroy($id)
    {
        // LAYER 2 — Permission check
        abort_unless(Gate::allows('service_assign_delete'), 403);

        // LAYER 3 — Ownership: ต้องเป็น record ของตัวเอง
        //           firstOrFail() ป้องกัน null pointer ด้วย
        $record = ServiceAssign::where('id', $id)
            ->where('serviceProvider', Auth::user()->username)
            ->firstOrFail(); // 404 ถ้าไม่ใช่ของตัวเอง (ไม่ใช่ 500)

        $record->delete();

        return response()->json(['success' => 'Successfully.']);
    }
}