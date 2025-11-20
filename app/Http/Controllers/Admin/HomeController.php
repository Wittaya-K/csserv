<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\ServiceAssign;
use App\User;
use App\Models\ServiceRequest;
use App\Models\Priority;
use App\Models\ServiceStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController
{
    public function index()
    {
        // $serviceRequests = ServiceRequest::get();
        $departments = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
		$prioritys = Priority::orderBy('id')->get();
		$users = DB::table('users')->where('username','!=',NULL)->get();
        $userStaffs = DB::table('users')
                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->where('roles.title', 'Staff')
                    ->orWhere('roles.title', 'Admin')
                    ->where('username','!=',NULL)
                    ->select('users.*')
                    ->get();

        $serviceRequestYears = ServiceRequest::select(DB::raw('YEAR(serviceDateTime) as selectYear'))
            ->distinct()
            ->orderBy('selectYear', 'asc')
            ->pluck('selectYear');

        $year = now()->year;   // ปีที่อยากดึง (เช่น ปีปัจจุบัน)

        $allMonths = collect(range(1,12))->map(fn ($m) => sprintf('%d-%02d', $year, $m));

        $monthObjects = $allMonths->map(function ($m)  {

            return (object) [
                'month'     => Carbon::parse("$m-01")->locale('th')->isoFormat('MMM YYYY') , // เดือนในรูปแบบ "ม.ค. 2025"
                'valMouths' => Carbon::parse("$m-01")->locale('en')->isoFormat('MM'), // เพิ่ม property valMouths เพื่อเก็บค่ารูปแบบ '2025-01' '2025-02' ...
                // 'valMouths' => $m, // เพิ่ม property valMouths เพื่อเก็บค่ารูปแบบ '2025-01' '2025-02' ...
            ];
        });

        $serviceRequests = null;
        // ตรวจสอบสิทธิ์การเข้าถึง
		$roles = Auth::user()->roles;
		foreach ($roles as $role) {
			if($role->title == 'Admin'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				// $serviceRequest = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
				$serviceRequests = ServiceRequest::get();
			}
			if($role->title == 'Staff'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				// $serviceRequest = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
				$serviceRequests = ServiceRequest::get();
			}
			if($role->title == 'User'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				$serviceRequests = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
			}
			if($role->title == 'Executive'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				$serviceRequests = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
			}
		}

		$servicesStatus = ServiceStatus::orderBy('id')->get();
        $dateToday = date("Y-m-d"); //วันที่ปัจจุบัน
        $allServiceRequest = ServiceRequest::count(); //จำนวนรายการหมวดหมู่ทั้งหมด
        $todayServiceRequest = ServiceRequest::whereDate('serviceDateTime','=',$dateToday)->count(); //จำนวนรายการตารางงานประจำวันนี้
        $comingServiceRequest = ServiceRequest::whereDate('serviceDateTime','>',$dateToday)->count(); //จำนวนรายการงานตามกำหนดการที่กำลังจะมาถึง
        $countUsers = User::orderBy('id')->count(); //ดึงข้อมูลจากตาราง tbl_related_service_requests มาแสดง

		$today = date("dmY");
		$rand = sprintf("%04d", rand(0,9999));
		$requestId = $today . $rand;

        return view('home',compact('serviceRequests','departments','service_assigns','requestId','prioritys','users','servicesStatus','allServiceRequest','todayServiceRequest','comingServiceRequest','countUsers','userStaffs','monthObjects','serviceRequestYears'));
    }
}
