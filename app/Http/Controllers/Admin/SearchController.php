<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\ServiceRequest;
use App\Models\ServiceAssign;
use App\User;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('search_access'), 403);
        
        $serviceRequests = ServiceRequest::select(DB::raw('DATE_FORMAT(serviceDateTime, "%d-%m-%Y") as serviceDateTime'))->get();
        $departments = Department::orderBy('id')->get(); 
        $serviceRequests = ServiceRequest::orderBy('id')->get();
        $serviceAssigns = ServiceAssign::orderBy('id')->get(); 
        $users = User::orderBy('id')->whereNotNull('username')->get();

        return view('admin.search.index', compact('departments','serviceRequests','users','serviceAssigns'));
    }
    public function search(Request $request)
    {
        
        $query = ServiceRequest::query();

        if ($request->has('serviceDateTime')) {
            $query->where('serviceDateTime', 'LIKE', '%' . $request->input('serviceDateTime') . '%');
        }

        if ($request->has('serviceDepartment')) {
            $query->where('serviceDepartment', 'LIKE', '%' . $request->input('serviceDepartment') . '%');
        }

        if ($request->has('serviceName')) {
            $query->where('serviceName', 'LIKE', '%' . $request->input('serviceName') . '%');
        }

        if ($request->has('serviceProvider')) {
            $query->where('serviceProvider', 'LIKE', '%' . $request->input('serviceProvider') . '%');
        }
        
        $providers = $query->get();
        return response()->json($providers);
    }
    public function serviceProviderSearch(Request $request)
    {

        $selectYear = $request->input(key: 'selectYear'); //2025
        $selectMonth = $request->input('selectMonth'); //09
        $selectServiceProvider = $request->input('selectServiceProvider');

                // ดึงจำนวน grouped ตาม serviceStatus
        $serviceRequests = ServiceRequest::select('service_request.id','service_assign.serviceName','service_request.serviceDateTime')
            ->join('service_status','service_status.serviceStatus','=','service_request.serviceStatus')
            ->join('service_provider','service_provider.reqid','=','service_request.id')
            ->join('service_assign','service_assign.id','=','service_request.serviceName')
            ->where('service_provider.serviceProvider','=',$selectServiceProvider)
            ->whereYear('service_request.serviceDateTime', $selectYear)
            ->whereMonth('service_request.serviceDateTime', $selectMonth)
            ->get();
        // dd($serviceRequests);
        // $query = ServiceRequest::query(); //ดึงข้อมูลจากตาราง service_request

        // if ($request->has('selectServiceProvider')) {
        //     // ค้นหาข้อมูลที่ตรงกับชื่อผู้ให้บริการ
        //     $query->select('service_request.id','service_assign.serviceName','service_request.serviceDateTime');
        //     $query->join('service_assign','service_assign.id','=','service_request.serviceName');
        //     $query->where('service_request.serviceProvider', 'LIKE', '%' . $request->input('selectServiceProvider') . '%');
        // }

        // $providers = $query->get();
        return response()->json(['status' => true, 'data' => $serviceRequests, 'message' => 'Search successful']);
    }
    public function serviceRequestSearch(Request $request)
    {
        // dd($request->input('selectServiceProvider'));

        if ($request->has('selectServiceProvider')) {
            $selectServiceProvider = $request->input('selectServiceProvider');
            // ค้นหาข้อมูลที่ตรงกับชื่อผู้ให้บริการ
            $serviceRequestAll = ServiceRequest::count(); //ดึงข้อมูลจากตาราง service_request
            $serviceRequestCompleted = ServiceRequest::where('serviceStatus','=','Completed')->where('serviceProvider','=',$selectServiceProvider)->count();
            $serviceRequestInProgress = ServiceRequest::where('serviceStatus','=','InProgress')->where('serviceProvider','=',$selectServiceProvider)->count();
            $serviceRequestInCancelled = ServiceRequest::where('serviceStatus','=','Cancelled')->where('serviceProvider','=',$selectServiceProvider)->count();
            $serviceDepartmentTypeIn = ServiceRequest::where('serviceDepartmentType','=','ภายในคณะวิทยาศาสตร์')->where('serviceProvider','=',$selectServiceProvider)->count();
            $serviceDepartmentTypeOut = ServiceRequest::where('serviceDepartmentType','=','ภายนอกคณะวิทยาศาสตร์')->where('serviceProvider','=',$selectServiceProvider)->count();

            $days = ServiceRequest::selectRaw("
                COUNT(CASE WHEN TIMESTAMPDIFF(DAY, created_at, updated_at) BETWEEN 0  AND 3  THEN 1 END) AS d_1_3,
                COUNT(CASE WHEN TIMESTAMPDIFF(DAY, created_at, updated_at) BETWEEN 1  AND 7  THEN 1 END) AS d_1_7,
                COUNT(CASE WHEN TIMESTAMPDIFF(DAY, created_at, updated_at) BETWEEN 1  AND 15 THEN 1 END) AS d_1_15,
                COUNT(CASE WHEN TIMESTAMPDIFF(DAY, created_at, updated_at) BETWEEN 1 AND 30 THEN 1 END) AS d_1_30,
                COUNT(CASE WHEN TIMESTAMPDIFF(DAY, created_at, updated_at) BETWEEN 1 AND 60 THEN 1 END) AS d_1_60")->where('serviceProvider','=',$selectServiceProvider)->first();
            
            $d_1_3  = $days->d_1_3;
            $d_1_7  = $days->d_1_7;
            $d_1_15 = $days->d_1_15;
            $d_1_30 = $days->d_1_30;
            $d_1_60 = $days->d_1_60;

            $d_1_3_percent  = ($d_1_3 / $serviceRequestAll) * 100; // คำนวณเปอร์เซ็นต์ของ 1-3 วัน
            $d_1_7_percent  = ($d_1_7 / $serviceRequestAll) * 100; // คำนวณเปอร์เซ็นต์ของ 1-7 วัน
            $d_1_15_percent = ($d_1_15 / $serviceRequestAll) * 100; // คำนวณเปอร์เซ็นต์ของ 1-15 วัน
            $d_1_30_percent = ($d_1_30 / $serviceRequestAll) * 100; // คำนวณเปอร์เซ็นต์ของ 1-30 วัน
            $d_1_60_percent = ($d_1_60 / $serviceRequestAll) * 100; // คำนวณเปอร์เซ็นต์ของ 1-60 วัน
        }
        $data = [
            'serviceRequestAll' => $serviceRequestAll,
            'serviceRequestCompleted' => $serviceRequestCompleted,
            'serviceRequestInProgress' => $serviceRequestInProgress,
            'serviceRequestInCancelled'  => $serviceRequestInCancelled,
            'serviceDepartmentTypeIn'  => $serviceDepartmentTypeIn,
            'serviceDepartmentTypeOut'  => $serviceDepartmentTypeOut,
            'd_1_3_percent' => $d_1_3_percent,
            'd_1_7_percent' => $d_1_7_percent,
            'd_1_15_percent' => $d_1_15_percent,
            'd_1_30_percent' => $d_1_30_percent,
            'd_1_60_percent' => $d_1_60_percent,
        ];
        
        return response()->json(['status' => true, 'data' => $data, 'message' => 'Search successful']);
    }
    public function serviceRequestYearSearch(Request $request)
    {
        if ($request->has('selectServiceProvider')) {
            $selectServiceProvider = $request->input('selectServiceProvider');
            $year = now()->year;   // ปีที่อยากดึง (เช่น ปีปัจจุบัน)

            /*
            |--------------------------------------------------------------
            | 1) ดึงข้อมูลสรุปด้วย SQL
            |    - DATE_FORMAT(...) คืนค่าเป็น 2025-01, 2025-02 …
            |    - SUM(status = 'xxx') นับเฉพาะแถวที่เงื่อนไขเป็นจริง
            |--------------------------------------------------------------
            */
            $monthlyStats = ServiceRequest::selectRaw("
                    DATE_FORMAT(created_at,'%Y-%m')  AS month,
                    SUM(serviceStatus = 'Completed')    AS completed_count,
                    SUM(serviceStatus = 'InProgress')   AS in_progress_count,
                    SUM(serviceStatus = 'Cancelled')    AS canceled_count
                ")
                ->whereYear('created_at', $year)          // จำกัดปี (หรือใช้ช่วงวันที่ก็ได้)
                ->where('serviceProvider','=',$selectServiceProvider)
                ->groupByRaw("DATE_FORMAT(created_at,'%Y-%m')")
                ->orderBy('month')
                ->get();

            /*
            |--------------------------------------------------------------
            | 2) เติมเดือนที่ไม่มีข้อมูลให้ครบ 12 เดือน
            |--------------------------------------------------------------
            */
            $allMonths = collect(range(1,12))->map(fn ($m) => sprintf('%d-%02d', $year, $m));

            $statsComplete = $allMonths->map(function ($m) use ($monthlyStats) {
                $row = $monthlyStats->firstWhere('month', $m);

                return (object) [
                    'month'     => Carbon::parse("$m-01")->locale('th')->isoFormat('MMM YYYY') ,
                    'Completed' => $row->completed_count    ?? 0,
                    'InProgress'=> $row->in_progress_count  ?? 0,
                    'Cancelled' => $row->canceled_count     ?? 0,
                ];
            });

            /*
            |--------------------------------------------------------------
            | 3) ตอนส่งต่อไปยัง View / API
            |--------------------------------------------------------------
            */
        }
        return response()->json(['status' => true, 'statsComplete' => $statsComplete, 'message' => 'Search successful']);
    }
}