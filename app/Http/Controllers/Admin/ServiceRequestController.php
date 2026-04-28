<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Department;
use App\Models\ServiceAssign;
use App\Models\Priority;
use App\Models\ServiceStatus;
use App\Models\ServiceRequestNote;
use App\Models\ServiceRequestHistory;
use App\Models\ServiceRequestMessage;
use App\Models\ServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ServiceRequestController extends Controller
{
    // ============================================================
    // LAYER 1: Authentication — middleware('auth') ใน web.php ✅
    // LAYER 2: Gate::allows  — permission check ทุก method ✅
    // LAYER 3: Ownership     — scoped query ตาม role ✅
    // LAYER 4: Input/Output  — validate + only() + path traversal ✅
    // ============================================================

    // ----------------------------------------------------------------
    // Helper: ดึง ServiceRequest โดย scope ตาม role อัตโนมัติ
    // ใช้ทดแทนการเขียน foreach role ซ้ำหลายจุด
    // ----------------------------------------------------------------
    private function scopedQuery()
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('title');

        if ($roles->contains('Admin')) {
            return ServiceRequest::query();
        }

        if ($roles->contains('Staff')) {
            return ServiceRequest::whereIn('id', function ($q) use ($user) {
                $q->select('reqid')
                  ->from('service_provider')
                  ->where('serviceProvider', $user->username);
            });
        }

        // User และ Executive เห็นเฉพาะคำขอของตัวเอง
        return ServiceRequest::where('serviceRecipient', $user->username);
    }

    // ----------------------------------------------------------------
    // index — หน้าแรก (แสดง form สร้างคำขอ)
    // ----------------------------------------------------------------
    public function index()
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        $departments     = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
        $prioritys       = Priority::orderBy('id')->get();
        $users           = DB::table('users')->whereNotNull('username')->get();
        $servicesStatus  = ServiceStatus::orderBy('id')->get();

        // สร้าง requestId สำหรับ form (ไม่ใช้ record จริงใน DB เพื่อหลีกเลี่ยง race condition)
        $today     = date('dmY');
        $requestId = $today . '0001';

        return view('admin.service_requests.index', compact(
            'departments', 'service_assigns', 'requestId', 'prioritys', 'users', 'servicesStatus'
        ));
    }

    // ----------------------------------------------------------------
    // create — หน้าสร้างคำขอใหม่
    // ----------------------------------------------------------------
    public function create()
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_create'), 403);

        $departments     = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
        $prioritys       = Priority::orderBy('id')->get();
        $users           = DB::table('users')->whereNotNull('username')->get();
        $staffUsers      = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where(function ($q) {
                $q->where('roles.title', 'Staff')->orWhere('roles.title', 'Admin');
            })
            ->whereNotNull('users.username')
            ->select('users.*')
            ->get();
        $servicesStatus  = ServiceStatus::orderBy('id')->get();

        $lastRequest = ServiceRequest::orderByDesc('id')->first();
        $nextNum     = $lastRequest ? $lastRequest->id + 1 : 1;
        $requestId   = date('dmY') . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        return view('admin.service_requests.create', compact(
            'departments', 'service_assigns', 'requestId', 'prioritys', 'users', 'servicesStatus', 'staffUsers'
        ));
    }

    // ----------------------------------------------------------------
    // edit — หน้าแก้ไขคำขอ
    // LAYER 3: scopedQuery() — ป้องกัน user เข้า record คนอื่น
    // ----------------------------------------------------------------
    public function edit($id)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_edit'), 403);

        // LAYER 3 — Ownership: ดึงเฉพาะ record ที่มีสิทธิ์
        $serviceRequests = $this->scopedQuery()->findOrFail($id);

        $fullName        = DB::table('users')->where('username', $serviceRequests->serviceRecipient)->first();
        $departments     = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
        $prioritys       = Priority::orderBy('id')->get();
        $users           = DB::table('users')->whereNotNull('username')->get();
        $staffUsers      = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where(function ($q) {
                $q->where('roles.title', 'Staff')->orWhere('roles.title', 'Admin');
            })
            ->whereNotNull('users.username')
            ->select('users.*')
            ->get();
        $servicesStatus          = ServiceStatus::orderBy('id')->get();
        $serviceRequestHistory   = ServiceRequestHistory::where('reqid', $id)->get();
        $ServiceRequestMessages  = ServiceRequestMessage::where('reqid', $id)->get();
        $serviceRequestHistorys  = ServiceRequestHistory::where('reqid', $id)->first();

        $serviceRequestHistoryServiceName        = '';
        $serviceRequestHistoryDateTime           = '';
        $serviceRequestHistoryDateTimeUpdate     = '';
        $serviceRequestHistoryDayAmounts         = 0;
        $chatTitle                               = '';

        foreach ($service_assigns as $service_assign) {
            if ($serviceRequests->serviceName == $service_assign->id) {
                $chatTitle = $service_assign->serviceName;
            }
        }

        if ($serviceRequestHistorys != null) {
            foreach ($service_assigns as $service_assign) {
                if ($serviceRequestHistorys->serviceHistoryName == $service_assign->id) {
                    $serviceRequestHistoryServiceName = $service_assign->serviceName;
                }
            }
            $serviceRequestHistoryDateTime       = Carbon::parse($serviceRequestHistorys->serviceHistoryDateTime);
            $serviceRequestHistoryDateTimeUpdate = Carbon::parse($serviceRequestHistorys->updated_at);
            $serviceRequestHistoryDayAmounts     = $serviceRequestHistoryDateTime->diffInDays($serviceRequestHistoryDateTimeUpdate);
        }

        $serviceDateTime = Carbon::parse($serviceRequests->serviceDateTime);
        $updated_at      = Carbon::parse($serviceRequests->updated_at);
        $dayAmounts      = $serviceDateTime->diffInDays($updated_at);
        $serviceDueDate  = $serviceRequests->serviceDateTime;

        $priorityName = $this->resolvePriorityName($dayAmounts);
        $serviceRequestHistoryDayAmountsPriorityName = $this->resolvePriorityName($serviceRequestHistoryDayAmounts);

        $serviceRequest     = ServiceRequest::where('id', $id)->orderByDesc('id')->first();
        $requestId          = $serviceRequest->serviceRequestNumber;
        $serviceRequestName = ServiceRequest::select('serviceRequestName')
            ->where('serviceRequestNumber', $serviceRequest->serviceRequestNumber)
            ->first();
        $requestName = $serviceRequestName->serviceRequestName ?? '';

        return view('admin.service_requests.edit', compact(
            'departments', 'service_assigns', 'requestId', 'prioritys', 'users', 'servicesStatus',
            'serviceRequests', 'chatTitle', 'serviceRequestHistory', 'ServiceRequestMessages',
            'serviceRequestHistoryServiceName', 'priorityName', 'serviceRequestHistoryDayAmountsPriorityName',
            'fullName', 'serviceDueDate', 'staffUsers', 'requestName'
        ));
    }

    // ----------------------------------------------------------------
    // view — หน้าดูรายละเอียดคำขอ
    // LAYER 3: scopedQuery() — ป้องกัน user เข้า record คนอื่น
    // ----------------------------------------------------------------
    public function view($id)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_show'), 403);

        // LAYER 3 — Ownership
        $serviceRequests = $this->scopedQuery()->findOrFail($id);

        $fullName                = DB::table('users')->where('username', $serviceRequests->serviceRecipient)->first();
        $departments             = Department::orderBy('id')->get();
        $service_assigns         = ServiceAssign::orderBy('id')->get();
        $prioritys               = Priority::orderBy('id')->get();
        $users                   = DB::table('users')->whereNotNull('username')->get();
        $servicesStatus          = ServiceStatus::orderBy('id')->get();
        $serviceRequest          = ServiceRequest::where('id', $id)->orderByDesc('id')->first();
        $serviceRequestHistory   = ServiceRequestHistory::where('reqid', $id)->get();
        $ServiceRequestMessages  = ServiceRequestMessage::where('reqid', $id)->get();
        $serviceRequestHistorys  = ServiceRequestHistory::where('reqid', $id)->first();
        $serviceProviders        = ServiceProvider::orderBy('id')->get();

        $serviceRequestHistoryServiceName    = '';
        $serviceRequestHistoryDateTime       = '';
        $serviceRequestHistoryDateTimeUpdate = '';
        $serviceRequestHistoryDayAmounts     = 0;
        $chatTitle                           = '';

        foreach ($service_assigns as $service_assign) {
            if ($serviceRequests->serviceName == $service_assign->id) {
                $chatTitle = $service_assign->serviceName;
            }
        }

        if ($serviceRequestHistorys != null) {
            foreach ($service_assigns as $service_assign) {
                if ($serviceRequestHistorys->serviceHistoryName == $service_assign->id) {
                    $serviceRequestHistoryServiceName = $service_assign->serviceName;
                }
            }
            $serviceRequestHistoryDateTime       = Carbon::parse($serviceRequestHistorys->serviceHistoryDateTime);
            $serviceRequestHistoryDateTimeUpdate = Carbon::parse($serviceRequestHistorys->updated_at);
            $serviceRequestHistoryDayAmounts     = $serviceRequestHistoryDateTime->diffInDays($serviceRequestHistoryDateTimeUpdate);
        }

        $serviceDateTime = Carbon::parse($serviceRequests->serviceDateTime);
        $updated_at      = Carbon::parse($serviceRequests->updated_at);
        $dayAmounts      = $serviceDateTime->diffInDays($updated_at);

        $priorityName = $this->resolvePriorityName($dayAmounts);
        $serviceRequestHistoryDayAmountsPriorityName = $this->resolvePriorityName($serviceRequestHistoryDayAmounts);

        $requestId = $serviceRequest->serviceRequestNumber ?? '';

        return view('admin.service_requests.view', compact(
            'departments', 'service_assigns', 'requestId', 'prioritys', 'users', 'servicesStatus',
            'serviceRequests', 'chatTitle', 'serviceRequestHistory', 'ServiceRequestMessages',
            'serviceRequestHistoryServiceName', 'priorityName', 'serviceRequestHistoryDayAmountsPriorityName',
            'fullName', 'serviceProviders'
        ));
    }

    // ----------------------------------------------------------------
    // list — ดึงรายการคำขอ (JSON สำหรับ DataTable)
    // LAYER 3: scopedQuery() — แต่ละ role เห็นเฉพาะ record ของตัวเอง
    // LAYER 4: only() — ส่งเฉพาะ field ที่จำเป็น
    // ----------------------------------------------------------------
    public function list()
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 3 — Ownership: scopedQuery กรองตาม role อัตโนมัติ
        $serviceRequests = $this->scopedQuery()->orderByDesc('id')->get();

        // LAYER 4 — Output filter: ส่งเฉพาะ field ที่จำเป็น
        $data = $serviceRequests->map(fn($r) => $r->only([
            'id',
            'serviceRequestNumber',
            'serviceRequestName',
            'serviceName',
            'serviceDepartment',
			'serviceDescription',
            'serviceStatus',
            'serviceDateTime',
            'serviceRecipient',
            'serviceFlag',
        ]));

        return response()->json(['status' => true, 'data' => $data]);
    }

    // ----------------------------------------------------------------
    // save — บันทึกคำขอใหม่
    // LAYER 2: service_request_create
    // LAYER 4: validate + only() + serviceRecipient บังคับจาก Auth
    // ----------------------------------------------------------------
    public function save(Request $request, $id = '')
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_create'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'serviceRequestNumber' => 'required|string|max:50',
            'serviceName'          => 'required|integer|exists:service_assign,id',
            'serviceDepartment'    => 'required',
            'serviceRecipient'     => 'required|string',
            'serviceDescription'   => 'nullable|string|max:2000',
            'serviceStatusName'    => 'nullable|string|max:100',
            'serviceFileUpload'    => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()]);
        }

        // LAYER 4 — File upload: ใช้ hashName() ป้องกัน path traversal
        $fileName = '-';
        if ($request->hasFile('serviceFileUpload')) {
            $file     = $request->file('serviceFileUpload');
            $fileName = $file->hashName(); // สุ่มชื่อไฟล์ ป้องกัน path traversal
            $file->move(public_path('uploads'), $fileName);
        }

        $serviceDepartment = $request->input('serviceDepartment');
        $departmentType    = $request->input('departmentType');
        $requestName       = $request->input('request_name');
        $serviceStatusName = $request->input('serviceStatusName');

        // สร้างหน่วยงานใหม่ถ้าเลือก 'etc'
        if ($serviceDepartment === 'etc') {
            $validator2 = Validator::make($request->all(), [
                'departmentName' => 'required|string|max:255',
                'departmentType' => 'required|string|max:100',
            ]);
            if ($validator2->fails()) {
                return response()->json(['status' => false, 'error' => $validator2->errors()]);
            }

            $department     = Department::create($request->only(['departmentName', 'departmentType']));
            $departmentId   = $department->id;
            $departmentType = $department->departmentType;
        } else {
            $departmentId   = $serviceDepartment;
        }

        // LAYER 4 — serviceRecipient บังคับจาก Auth ถ้าเป็น User
        //           Admin/Staff สามารถระบุ serviceRecipient ให้คนอื่นได้
        $user  = Auth::user();
        $roles = $user->roles->pluck('title');

        $serviceRecipient = $roles->intersect(['Admin', 'Staff'])->isNotEmpty()
            ? $request->input('serviceRecipient')
            : $user->username; // User/Executive บังคับเป็นของตัวเองเสมอ

        $serviceRequest = ServiceRequest::create([
            'serviceRequestNumber' => $request->input('serviceRequestNumber'),
            'serviceDescription'   => $request->input('serviceDescription'),
            'serviceName'          => $request->input('serviceName'),
            'serviceDepartment'    => $departmentId,
            'serviceDepartmentType'=> $departmentType ?? null,
            'serviceRequestName'   => $requestName,
            'serviceFileUpload'    => $fileName,
            'serviceStatus'        => $serviceStatusName,
            'serviceDateTime'      => Carbon::now(),
            'servicePriority'      => null,
            'serviceRecipient'     => $serviceRecipient,
            'serviceProvider'      => null,
        ]);

        // บันทึก ServiceProvider ถ้ามี
        if ($serviceRequest) {
            $serviceProviderInput = $request->input('serviceProvider', []);
            if (!is_array($serviceProviderInput)) {
                $serviceProviderInput = [$serviceProviderInput];
            }
            foreach ($serviceProviderInput as $provider) {
                ServiceProvider::create([
                    'reqid'           => $serviceRequest->id,
                    'serviceProvider' => $provider,
                ]);
            }

            return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
        }

        return response()->json(['status' => false, 'message' => 'เกิดข้อผิดพลาด'], 500);
    }

    // ----------------------------------------------------------------
    // update — แก้ไขคำขอ
    // LAYER 2: service_request_edit
    // LAYER 3: scopedQuery — ป้องกัน edit record คนอื่น
    // LAYER 4: validate + only()
    // ----------------------------------------------------------------
    public function update(Request $request, $id = '')
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_edit'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'serviceRequestNumber' => 'required|string|max:50',
            'serviceName'          => 'required|integer|exists:service_assign,id',
            'serviceDepartment'    => 'required',
            'serviceRecipient'     => 'required|string',
            'serviceDescription'   => 'nullable|string|max:2000',
            'serviceStatusName'    => 'nullable|string|max:100',
            'serviceFileUpload'    => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            'id'                   => 'required|integer|exists:service_request,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()]);
        }

        // LAYER 3 — Ownership: ป้องกัน edit record คนอื่น
        $existingRequest = $this->scopedQuery()->findOrFail($request->input('id'));

        // LAYER 4 — File upload: ใช้ hashName() ป้องกัน path traversal
        $fileName = $existingRequest->serviceFileUpload ?? '-';
        if ($request->hasFile('serviceFileUpload')) {
            $file     = $request->file('serviceFileUpload');
            $fileName = $file->hashName();
            $file->move(public_path('uploads'), $fileName);
        }

        $serviceDepartment = $request->input('serviceDepartment');
        $requestName       = $request->input('request_name');
        $serviceStatusName = $request->input('serviceStatusName');

        // สร้างหน่วยงานใหม่ถ้าเลือก 'etc'
        if ($serviceDepartment === 'etc') {
            $validator2 = Validator::make($request->all(), [
                'departmentName' => 'required|string|max:255',
                'departmentType' => 'required|string|max:100',
            ]);
            if ($validator2->fails()) {
                return response()->json(['status' => false, 'error' => $validator2->errors()]);
            }
            $department    = Department::create($request->only(['departmentName', 'departmentType']));
            $departmentId  = $department->id;
            $departmentType = $department->departmentType;
        } else {
            $dept          = Department::findOrFail($serviceDepartment);
            $departmentId  = $dept->id;
            $departmentType = $dept->departmentType;
        }

        // บันทึก History ก่อน update
        ServiceRequestHistory::create([
            'reqid'                        => $existingRequest->id,
            'serviceRequestHistoryNumber'  => $existingRequest->serviceRequestNumber,
            'serviceHistoryDescription'    => $existingRequest->serviceDescription,
            'serviceHistoryName'           => $existingRequest->serviceName,
            'serviceHistoryDepartment'     => $existingRequest->serviceDepartment,
            'serviceHistoryDepartmentType' => $existingRequest->serviceDepartmentType,
            'serviceRequestName'           => $requestName,
            'serviceHistoryFileUpload'     => $existingRequest->serviceFileUpload,
            'serviceHistoryStatus'         => $existingRequest->serviceStatus,
            'serviceHistoryDateTime'       => $existingRequest->serviceDateTime,
            'serviceHistoryPriority'       => null,
            'serviceHistoryRecipient'      => $existingRequest->serviceRecipient,
            'serviceHistoryProvider'       => $existingRequest->serviceProvider,
        ]);

        // LAYER 4 — only() field ที่อนุญาต
        $existingRequest->update([
            'serviceRequestNumber'  => $request->input('serviceRequestNumber'),
            'serviceDescription'    => $request->input('serviceDescription'),
            'serviceName'           => $request->input('serviceName'),
            'serviceDepartment'     => $departmentId,
            'serviceDepartmentType' => $departmentType,
            'serviceRequestName'    => $requestName,
            'serviceFileUpload'     => $fileName,
            'serviceStatus'         => $serviceStatusName,
            'serviceDateTime'       => Carbon::now(),
            'servicePriority'       => null,
            'serviceRecipient'      => $request->input('serviceRecipient'),
            'serviceProvider'       => $request->input('serviceProvider'),
        ]);

        return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
    }

    // ----------------------------------------------------------------
    // find — ดึงข้อมูล record เดี่ยว (JSON)
    // LAYER 3: scopedQuery — ป้องกัน access record คนอื่น
    // LAYER 4: only() — ส่งเฉพาะ field ที่จำเป็น
    // ----------------------------------------------------------------
    public function find($id)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 3 — Ownership
        $serviceRequest = $this->scopedQuery()->findOrFail($id);

        // LAYER 4 — Output filter
        return response()->json([
            'status' => true,
            'data'   => $serviceRequest->only([
                'id', 'serviceRequestNumber', 'serviceRequestName',
                'serviceName', 'serviceDepartment', 'serviceDepartmentType',
                'serviceDescription', 'serviceStatus', 'serviceDateTime',
                'serviceRecipient', 'serviceProvider', 'serviceFileUpload',
                'serviceFlag',
            ]),
        ]);
    }

    // ----------------------------------------------------------------
    // delete — ลบคำขอ
    // LAYER 3: scopedQuery — ป้องกัน delete record คนอื่น
    // ----------------------------------------------------------------
    public function delete($id)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_delete'), 403);

        // LAYER 3 — Ownership
        $serviceRequest = $this->scopedQuery()->findOrFail($id);
        $serviceRequest->delete();

        return response()->json(['status' => true, 'message' => 'ลบสำเร็จ!']);
    }

    // ----------------------------------------------------------------
    // download — ดาวน์โหลดไฟล์แนบ
    // LAYER 3: scopedQuery — ตรวจว่า record นั้นเป็นของตัวเองก่อน
    // LAYER 4: Path traversal protection — ใช้ basename() + whitelist ext
    // ----------------------------------------------------------------
    public function download($id)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 3 — Ownership: ดึง record เพื่อหา filename จริง
        //           ไม่รับ filename ตรงจาก URL parameter
        $serviceRequest = $this->scopedQuery()->findOrFail($id);

        $storedFileName = $serviceRequest->serviceFileUpload;

        // LAYER 4 — Path traversal: ป้องกัน ../../../etc/passwd
        if (!$storedFileName || $storedFileName === '-') {
            abort(404, 'ไม่มีไฟล์แนบ');
        }

        // basename() ตัด path traversal ออก
        $safeFileName = basename($storedFileName);
        $filePath     = public_path('uploads/' . $safeFileName);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // LAYER 4 — Whitelist MIME type ที่อนุญาต
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/png',
            'image/jpeg',
        ];

        $mimeType = mime_content_type($filePath);

        if (!in_array($mimeType, $allowedMimes)) {
            abort(403, 'ประเภทไฟล์ไม่ได้รับอนุญาต');
        }

        return Response::download($filePath, $safeFileName, ['Content-Type' => $mimeType]);
    }

    // ----------------------------------------------------------------
    // service_status_change — เปลี่ยนสถานะคำขอ
    // LAYER 3: scopedQuery — ป้องกัน update record คนอื่น
    // LAYER 4: validate input
    // ----------------------------------------------------------------
    public function service_status_change(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_edit'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'dataId'          => 'required|integer|exists:service_request,id',
            'serviceStatusName' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $id = $request->input('dataId');

        // LAYER 3 — Ownership
        $serviceRequest = $this->scopedQuery()->findOrFail($id);

        $serviceName    = ServiceAssign::where('id', $serviceRequest->serviceName)->first();
        $departmentName = Department::where('id', $serviceRequest->serviceDepartment)->first();
        $serviceStatusName = $request->input('serviceStatusName');

        // บันทึก History
        ServiceRequestHistory::create([
            'serviceRequestHistoryNumber' => $serviceRequest->serviceRequestNumber,
            'serviceHistoryDescription'   => $serviceRequest->serviceDescription,
            'serviceHistoryName'          => $serviceRequest->serviceName,
            'serviceHistoryDepartment'    => $serviceRequest->serviceDepartment,
            'serviceHistoryDepartmentType'=> $serviceRequest->serviceDepartmentType,
            'serviceHistoryFileUpload'    => $serviceRequest->serviceFileUpload,
            'serviceHistoryStatus'        => $serviceStatusName,
            'serviceHistoryDateTime'      => $serviceRequest->serviceDateTime,
            'serviceHistoryPriority'      => null,
            'serviceHistoryRecipient'     => $serviceRequest->serviceRecipient,
            'serviceHistoryProvider'      => $serviceRequest->serviceProvider,
        ]);

        // LAYER 4 — only field ที่ต้องการ update
        $serviceRequest->update(['serviceStatus' => $serviceStatusName]);

        return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
    }

    // ----------------------------------------------------------------
    // serviceFlag — ติดดาว / เอาดาวออก
    // LAYER 3: scopedQuery — ป้องกัน flag record คนอื่น
    // LAYER 4: validate input
    // ----------------------------------------------------------------
    public function serviceFlag(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_edit'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'dataId'    => 'required|integer|exists:service_request,id',
            'flagValue' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $id = $request->input('dataId');

        // LAYER 3 — Ownership: ดึง record ก่อน แล้วค่อย update
        $serviceRequest = $this->scopedQuery()->findOrFail($id);
        $serviceRequest->update(['serviceFlag' => $request->input('flagValue')]);

        return response()->json(['status' => true, 'message' => 'ติดดาวสำเร็จ']);
    }

    // ----------------------------------------------------------------
    // updateServiceRequestNote — บันทึก note
    // LAYER 3: ตรวจว่า serviceRequest นั้นเป็นของตัวเองก่อน
    // LAYER 4: validate input
    // ----------------------------------------------------------------
    public function updateServiceRequestNote(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_edit'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'serviceRequestId'     => 'required|integer|exists:service_request,id',
            'serviceRequestNumber' => 'required|string|max:50',
            'serviceRequestNote'   => 'required|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $id = $request->input('serviceRequestId');

        // LAYER 3 — Ownership: ตรวจว่า record นั้นเป็นของตัวเองก่อน
        $serviceRequest = $this->scopedQuery()->findOrFail($id);

        $ServiceRequestNote = ServiceRequestNote::create([
            'serviceRequestId'       => $id,
            'serviceRequestNumber'   => $request->input('serviceRequestNumber'),
            'serviceRequestNote'     => $request->input('serviceRequestNote'),
            'serviceRequestProvider' => $serviceRequest->serviceProvider, // ดึงจาก record จริง ไม่รับจาก request
        ]);

        return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ']);
    }

    // ----------------------------------------------------------------
    // serviceRequestMessage — ส่งข้อความในคำขอ
    // LAYER 3: ตรวจ ownership ก่อนบันทึก message
    // LAYER 4: validate input
    // ----------------------------------------------------------------
    public function serviceRequestMessage(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_edit'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'dataId'                => 'required|integer|exists:service_request,id',
            'serviceRequestMessage' => 'required|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $id   = $request->input('dataId');
        $user = Auth::user();

        // LAYER 3 — Ownership
        $serviceRequest = $this->scopedQuery()->findOrFail($id);

        $roles  = $user->roles->pluck('title');
        $sender = $user->username; // บังคับ sender จาก Auth เสมอ

        if ($roles->contains('Staff') || $roles->contains('Admin')) {
            $serviceProvider = ServiceProvider::where('reqid', $id)
                ->where('serviceProvider', $user->username)
                ->first();
            $sender = $serviceProvider->serviceProvider ?? $user->username;
        }

        ServiceRequestMessage::create([
            'reqid'                     => $id,
            'requestMessageServiceName' => $serviceRequest->serviceName,
            'requestMessageSender'      => $sender, // บังคับจาก Auth ไม่รับจาก request
            'requestMessage'            => $request->input('serviceRequestMessage'),
            'requestMessageDateTime'    => Carbon::now(),
        ]);

        return response()->json(['status' => true, 'message' => 'success']);
    }

    // ----------------------------------------------------------------
    // getTimeLine — ดู timeline ของคำขอ
    // LAYER 3: ตรวจ ownership ก่อน
    // LAYER 4: only() field ที่จำเป็น
    // ----------------------------------------------------------------
    public function getTimeLine($id)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 3 — Ownership: ตรวจว่ามีสิทธิ์เข้า record นี้ไหม
        $this->scopedQuery()->findOrFail($id);

        $serviceRequestNotes = ServiceRequestNote::join('service_request', 'service_request.id', '=', 'service_request_note.serviceRequestId')
            ->join('service_assign', 'service_assign.id', '=', 'service_request.serviceName')
            ->join('users', 'users.username', '=', 'service_request.serviceProvider')
            ->where('serviceRequestId', $id)
            ->get();

        if ($serviceRequestNotes->isNotEmpty()) {
            return response()->json(['status' => true, 'data' => $serviceRequestNotes]);
        }

        return response()->json(['status' => false, 'message' => 'ไม่พบข้อมูล']);
    }

    // ----------------------------------------------------------------
    // departmentType — ดึง department ตาม type
    // LAYER 4: validate input
    // ----------------------------------------------------------------
    public function departmentType(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'departmentType' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $data = Department::where('departmentType', $request->input('departmentType'))
            ->get(['id', 'departmentName', 'departmentType']); // LAYER 4 — only field ที่จำเป็น

        return response()->json(['status' => true, 'data' => $data]);
    }

    // ----------------------------------------------------------------
    // servicename — ดึง service ตาม serviceProvider
    // LAYER 4: validate input
    // ----------------------------------------------------------------
    public function servicename(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'serviceProvider' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 422);
        }

        $data = ServiceAssign::where('serviceProvider', $request->input('serviceProvider'))
            ->get(['id', 'serviceName', 'serviceProvider']); // LAYER 4 — only field ที่จำเป็น

        return response()->json(['status' => true, 'data' => $data]);
    }

    // ----------------------------------------------------------------
    // selectdepartments — ค้นหา staff ที่รับผิดชอบ department/service
    // ----------------------------------------------------------------
    public function selectdepartments(Request $request)
    {
        // LAYER 2
        abort_unless(Gate::allows('service_request_access'), 403);

        // LAYER 4 — Validate
        $validator = Validator::make($request->all(), [
            'department' => 'required|string|max:255',
            'service'    => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()]);
        }

        $service_id   = $request->input('service');
        $assign_tasks = DB::table('assign_tasks')->get();

        $usernames = $assign_tasks->filter(function ($item) use ($service_id) {
            return in_array($service_id, explode(',', $item->service_id));
        })->pluck('username')->all();

        $department    = $request->input('department');
        $department_id = DB::table('department')->where('department_name', $department)->value('id');

        if (!$department_id) {
            return response()->json(['status' => false, 'message' => 'ไม่พบหน่วยงาน']);
        }

        if (empty($usernames)) {
            return response()->json(['status' => false, 'message' => 'ไม่พบข้อมูล']);
        }

        // LAYER 4 — ส่งเฉพาะ field ที่จำเป็น
        $user = DB::table('users')->where('username', $usernames[0])->select('name', 'username')->first();

        return response()->json([
            'status' => true,
            'data'   => ['services' => $user->name ?? ''],
        ]);
    }

    // ----------------------------------------------------------------
    // Helper: แปลงจำนวนวันเป็นชื่อความเร่งด่วน
    // ----------------------------------------------------------------
    private function resolvePriorityName(int $days): string
    {
        return match (true) {
            $days <= 1  => 'ด่วนที่สุด',
            $days <= 3  => 'ด่วนมาก',
            $days <= 7  => 'ด่วน',
            default     => 'ปกติ',
        };
    }
}