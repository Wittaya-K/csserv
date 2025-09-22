<?php

namespace App\Http\Controllers\Admin;
use App\Models\ServiceAssign;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceRequest;
use App\Models\ServiceStatus;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportSumarryController
{
    public function index()
    {
        $userStaffs = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.title', 'Staff')
            ->orWhere('roles.title', 'Admin')
            ->where('username', '!=', NULL)
            ->select('users.*')
            ->get();

        $serviceRequestYears = ServiceRequest::select(DB::raw('YEAR(serviceDateTime) as selectYear'))
            ->distinct()
            ->orderBy('selectYear', 'asc')
            ->pluck('selectYear');

        $staffUsers = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.title', 'Staff')
            ->orWhere('roles.title', 'Admin')
            ->whereNotNull('users.username')
            ->select('users.*')
            ->get();

        $year = now()->year;   // ปีที่อยากดึง (เช่น ปีปัจจุบัน)

        $allMonths = collect(range(1,12))->map(fn ($m) => sprintf('%d-%02d', $year, $m));

        $monthObjects = $allMonths->map(function ($m)  {

            return (object) [
                'month'     => Carbon::parse("$m-01")->locale('th')->isoFormat('MMM YYYY') , // เดือนในรูปแบบ "ม.ค. 2025"
                'valMouths' => Carbon::parse("$m-01")->locale('en')->isoFormat('MM'), // เพิ่ม property valMouths เพื่อเก็บค่ารูปแบบ '2025-01' '2025-02' ...
                // 'valMouths' => $m, // เพิ่ม property valMouths เพื่อเก็บค่ารูปแบบ '2025-01' '2025-02' ...
            ];
        });

        $boxes = [
            [
                'color' => 'info',
                'count' => 120,
                'text' => 'รอดำเนินการ',
                'icon' => 'fas fa-tasks',
            ],
            [
                'color' => 'warning',
                'count' => 35,
                'text' => 'กำลังดำเนินการ',
                'icon' => 'fas fa-spinner',
            ],
            [
                'color' => 'success',
                'count' => 75,
                'text' => 'ดำเนินการเสร็จสิ้น',
                'icon' => 'fas fa-check-circle',
            ],
            [
                'color' => 'danger',
                'count' => 10,
                'text' => 'ยกเลิก',
                'icon' => 'fas fa-times',
            ],
        ];

        return view('admin.report_summary.index', compact('userStaffs', 'monthObjects', 'boxes','staffUsers'))
            ->with('serviceRequestYears', $serviceRequestYears);
    }

    public function search(Request $request)
    {
        // $groupServices = ['G1' => 1, 'G2'=> 2];
        $selectYear = $request->input(key: 'selectYear'); //2025
        $selectMonth = $request->input('selectMonth'); //09
        $selectServiceProvider = $request->input('selectServiceProvider');


        // $getServiceAssigns = ServiceAssign::get();
        // foreach ($getServiceAssigns as $serviceAssign) {
        //     echo "id: $serviceAssign->id\n"."serviceName: $serviceAssign->serviceName\n"."serviceProvider: $serviceAssign->serviceProvider\n"."serviceType: $serviceAssign->serviceType\n"."<br>";
        // }
        // echo '<pre>';
        // print_r($getServiceAssigns);
        // echo '</pre>';
        // dd($getServiceAssigns);

        // ดึง request ทั้งหมดในเดือนที่เลือก
        $serviceRequestSearch = ServiceRequest::whereYear('serviceDateTime', $selectMonth)->get();

        // ดึงจำนวน grouped ตาม serviceStatus
        $serviceRequests = ServiceRequest::select('service_request.serviceStatus', DB::raw('count(*) as total'))
            ->join('service_status','service_status.serviceStatus','=','service_request.serviceStatus')
            ->join('service_provider','service_provider.reqid','=','service_request.id')
            ->where('service_provider.serviceProvider','=',$selectServiceProvider)
            ->whereYear('service_request.serviceDateTime', $selectYear)
            ->whereMonth('service_request.serviceDateTime', $selectMonth)
            ->groupBy('service_status.serviceStatus')
            ->pluck('total', 'service_status.serviceStatus'); // ได้เป็น [ serviceStatus => total ]

        // ดึง serviceStatus ทั้งหมดที่มี
        $serviceStatus = ServiceStatus::pluck('serviceStatus'); // หรือ field อื่นที่เป็น key เช่น code

        // เตรียม array ให้มีทุก status แม้จะไม่มีข้อมูล (เป็น 0)
        $countServiceStatus = [];
        foreach ($serviceStatus as $status) {
            $countServiceStatus[$status] = $serviceRequests[$status] ?? 0;
        }
        
        // $itServices = ServiceAssign::where('serviceType', 'it')->get();
        // ดึงจำนวน grouped ตาม serviceName
        $itServiceName = ServiceRequest::select('service_request.serviceName', DB::raw('count(*) as total'))
                    ->join('service_assign','service_assign.id','=','service_request.serviceName')
                    ->where('service_assign.serviceProvider','=',$selectServiceProvider)
                    ->where('service_assign.serviceType', 'it')
                    ->groupBy('service_request.serviceName')
                    ->pluck('total', 'service_request.serviceName');

        // ดึง ServiceAssign ทั้งหมดที่มี
        $itServiceAssigns = ServiceAssign::where('serviceType', 'it')->pluck('id');

        // เตรียม array ให้มีทุก serviceAssigns แม้จะไม่มีข้อมูล (เป็น 0)
        $countItServiceAssign = [];
        foreach ($itServiceAssigns as $itServiceAssign) {
            $countItServiceAssign[$itServiceAssign] = $itServiceName[$itServiceAssign] ?? 0;
        }

        // $coordinateServices = ServiceAssign::where('serviceType', 'coordinate')->get();
        // ดึงจำนวน grouped ตาม serviceName
        $coordinateServiceName = ServiceRequest::select('service_request.serviceName', DB::raw('count(*) as total'))
                    ->join('service_assign','service_assign.id','=','service_request.serviceName')
                    ->where('service_assign.serviceProvider','=',$selectServiceProvider)
                    ->where('service_assign.serviceType', 'coordinate')
                    ->groupBy('service_request.serviceName')
                    ->pluck('total', 'service_request.serviceName');

        // ดึง ServiceAssign ทั้งหมดที่มี
        $coordinateServiceAssigns = ServiceAssign::where('serviceType', 'coordinate')->pluck('id');
        // dd($coordinateServiceAssigns);
        // เตรียม array ให้มีทุก serviceAssigns แม้จะไม่มีข้อมูล (เป็น 0)
        $countCoordinateServiceAssign = [];
        foreach ($coordinateServiceAssigns as $coordinateServiceAssign) {
            $countCoordinateServiceAssign[$coordinateServiceAssign] = $coordinateServiceName[$coordinateServiceAssign] ?? 0;
        }


        // ดึงจำนวน grouped ตาม serviceName
        $serviceName = ServiceRequest::select('service_request.serviceName', DB::raw('count(*) as total'))
                    ->join('service_assign','service_assign.id','=','service_request.serviceName')
                    ->where('service_assign.serviceProvider','=',$selectServiceProvider)
                    ->groupBy('service_request.serviceName')
                    ->pluck('total', 'service_request.serviceName');

        // ดึง ServiceAssign ทั้งหมดที่มี
        $serviceAssigns = ServiceAssign::pluck('id');

        // เตรียม array ให้มีทุก serviceAssigns แม้จะไม่มีข้อมูล (เป็น 0)
        $countServiceAssign = [];
        foreach ($serviceAssigns as $serviceAssign) {
            $countServiceAssign[$serviceAssign] = $serviceName[$serviceAssign] ?? 0;
        }

        // ดึงจำนวน grouped ตาม serviceName
        $departmentName = ServiceRequest::select('service_request.serviceDepartment', DB::raw('count(*) as total'))
                    ->join('department','department.id','=','service_request.serviceDepartment')
                    ->join('service_assign','service_assign.id','=','service_request.serviceName')
                    ->where('service_assign.serviceProvider','=',$selectServiceProvider)
                    ->groupBy('service_request.serviceDepartment')
                    ->pluck('total', 'service_request.serviceDepartment');

        // ดึง ServiceAssign ทั้งหมดที่มี
        $serviceDepartments = Department::pluck('id');

        // เตรียม array ให้มีทุก serviceAssigns แม้จะไม่มีข้อมูล (เป็น 0)
        $countDepartmentName = [];
        foreach ($serviceDepartments as $serviceDepartment) {
            $countDepartmentName[$serviceDepartment] = $departmentName[$serviceDepartment] ?? 0;
        }

        return response()->json(data: [
            // 'serviceRequestSearch' => $serviceRequestSearch,
            'countServiceStatus'   => $countServiceStatus, // array ที่มี key เป็น status
            'countDepartmentName'  => $countDepartmentName,
            'countItServiceAssign'           => $countItServiceAssign,
            'countCoordinateServiceAssign'   => $countCoordinateServiceAssign,
        ]);
    }
}
