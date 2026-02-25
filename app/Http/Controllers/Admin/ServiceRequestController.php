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

class ServiceRequestController extends Controller
{
	public function index(){

        $departments = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
		$prioritys = Priority::orderBy('id')->get();
		$users = DB::table('users')->where('username','!=',NULL)->get();
		$servicesStatus = ServiceStatus::orderBy('id')->get();
		$serviceRequest = serviceRequest::orderByDesc('id')->first();
        // $serviceRequestId = $serviceRequest->id; // 'EQ-000001'
        $serviceRequestId = 0;
        if ($serviceRequestId) {

            // Extract the numeric part from the last equipment ID
            $lastId = intval(substr($serviceRequest->id, 3));

            // Increment the numeric part by one
            $newId = $lastId + 1;

            // Format the new ID back into the "EQ-000001" format
            $serviceRequestId = 'RQ' . str_pad($newId, 6, '0', STR_PAD_LEFT);
        } else {
            // If no equipment ID exists, start with the first ID
            $serviceRequestId = '0001';
        }

		$today = date("dmY");
		$rand = sprintf("%04d", rand(0,9999));
		$requestId = $today . $serviceRequestId;

		return view('admin.service_requests.index',compact('departments','service_assigns','requestId','prioritys','users','servicesStatus'));
	}

    public function create(){
        $departments = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
		$prioritys = Priority::orderBy('id')->get();
		$users = DB::table('users')->where('username','!=',NULL)->get();
        $staffUsers = DB::table('users')
                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->where('roles.title', 'Staff')
                    ->orWhere('roles.title', 'Admin')
                    ->whereNotNull('users.username')
                    ->select('users.*')
                    ->get();
		$servicesStatus = ServiceStatus::orderBy('id')->get();
		$serviceRequest = serviceRequest::orderByDesc('id')->first();
        if($serviceRequest == null){
            $serviceRequestId = "001";
        }else{
            $serviceRequestId = "00000".$serviceRequest->id + 1;
        }

		$today = date("dmY");
		$rand = sprintf("%04d", rand(0,9999));
		$requestId = $today . $serviceRequestId;

		return view('admin.service_requests.create',compact('departments','service_assigns','requestId','prioritys','users','servicesStatus','staffUsers'));
	}

    public function edit($id){
        $serviceRequests = ServiceRequest::findOrFail($id);
        $fullName = DB::table('users')->where('username','=',$serviceRequests->serviceRecipient)->first();
        $departments = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
		$prioritys = Priority::orderBy('id')->get();
		$users = DB::table('users')->where('username','!=',NULL)->get();
		$servicesStatus = ServiceStatus::orderBy('id')->get();
		$serviceRequest = serviceRequest::orderByDesc('id')->first();
        $serviceRequestHistory = ServiceRequestHistory::where('reqid','=',$id)->get();
        $ServiceRequestMessages = ServiceRequestMessage::where('reqid','=',$id)->get();
        $serviceRequestHistorys = ServiceRequestHistory::where('reqid','=',$id)->first();
        $serviceRequestHistoryServiceName = '';
        $serviceRequestHistoryDateTime = '';
        $serviceRequestHistoryDateTimeUpdate = '';
        $serviceRequestHistoryDayAmounts = 0;
        $chatTitle = '';

        foreach ($service_assigns as $service_assign) {
            if($serviceRequests->serviceName == $service_assign->id){
                $chatTitle = $service_assign->serviceName;
            }
        }
        if($serviceRequestHistorys != null){
            foreach ($service_assigns as $service_assign) {
                if($serviceRequestHistorys->serviceHistoryName == $service_assign->id){
                    $serviceRequestHistoryServiceName = $service_assign->serviceName;
                }
            }
            // หาจำนวนวันที่ผ่านไปตั้งแต่วันที่บันทึกประวัติการให้บริการจาก service_request_history
            $serviceRequestHistoryDateTime  = Carbon::parse($serviceRequestHistorys->serviceHistoryDateTime);
            $serviceRequestHistoryDateTimeUpdate  = Carbon::parse($serviceRequestHistorys->updated_at);
            $serviceRequestHistoryDayAmounts = $serviceRequestHistoryDateTime->diffInDays($serviceRequestHistoryDateTimeUpdate);
        }

        // หาจำนวนวันที่ผ่านไปตั้งแต่วันที่บันทึกประวัติการให้บริการจาก service_request
        $serviceDateTime  = Carbon::parse($serviceRequests->serviceDateTime);
        $updated_at  = Carbon::parse($serviceRequests->updated_at);
        $dayAmounts = $serviceDateTime->diffInDays($updated_at);
        $serviceDueDate = $serviceRequests->serviceDateTime; // วันที่ต้องการใช้บริการ
        if($dayAmounts <= 1){
            $priorityName = 'ด่วนที่สุด';
        }elseif($dayAmounts <= 3){
            $priorityName = 'ด่วนมาก';
        }elseif($dayAmounts <= 7){
            $priorityName = 'ด่วน';
        }elseif($dayAmounts <= 14){
            $priorityName = 'ปกติ';
        }elseif($dayAmounts <= 30){
            $priorityName = 'ปกติ';
        }elseif($dayAmounts <= 60){
            $priorityName = 'ปกติ';
        }

        if($serviceRequestHistoryDayAmounts <= 1){
            $serviceRequestHistoryDayAmountsPriorityName = 'ด่วนที่สุด';
        }elseif($serviceRequestHistoryDayAmounts <= 3){
            $serviceRequestHistoryDayAmountsPriorityName = 'ด่วนมาก';
        }elseif($serviceRequestHistoryDayAmounts <= 7){
            $serviceRequestHistoryDayAmountsPriorityName = 'ด่วน';
        }elseif($serviceRequestHistoryDayAmounts <= 14){
            $serviceRequestHistoryDayAmountsPriorityName = 'ปกติ';
        }elseif($serviceRequestHistoryDayAmounts <= 30){
            $serviceRequestHistoryDayAmountsPriorityName = 'ปกติ';
        }elseif($serviceRequestHistoryDayAmounts <= 60){
            $serviceRequestHistoryDayAmountsPriorityName = 'ปกติ';
        }

        $requestId = $serviceRequest->serviceRequestNumber;

		return view('admin.service_requests.edit',compact('departments','service_assigns','requestId','prioritys','users','servicesStatus','serviceRequests','chatTitle','serviceRequestHistory','ServiceRequestMessages','serviceRequestHistoryServiceName','priorityName','serviceRequestHistoryDayAmountsPriorityName','fullName','serviceDueDate'));
	}

    public function view($id){
        $serviceRequests = ServiceRequest::findOrFail($id);
        $fullName = DB::table('users')->where('username','=',$serviceRequests->serviceRecipient)->first();
        $departments = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
		$prioritys = Priority::orderBy('id')->get();
		$users = DB::table('users')->where('username','!=',NULL)->get();
		$servicesStatus = ServiceStatus::orderBy('id')->get();
		$serviceRequest = serviceRequest::where('id','=',$id)->orderByDesc('id')->first();
        $serviceRequestHistory = ServiceRequestHistory::where('reqid','=',$id)->get();
        $ServiceRequestMessages = ServiceRequestMessage::where('reqid','=',$id)->get();
        $serviceRequestHistorys = ServiceRequestHistory::where('reqid','=',$id)->first();
        $serviceProviders = ServiceProvider::orderBy('id')->get();
        $serviceRequestHistoryServiceName = '';
        $serviceRequestHistoryDateTime = '';
        $serviceRequestHistoryDateTimeUpdate = '';
        $serviceRequestHistoryDayAmounts = 0;

        $chatTitle = '';
        foreach ($service_assigns as $service_assign) {
            if($serviceRequests->serviceName == $service_assign->id){
                $chatTitle = $service_assign->serviceName;
            }
        }
        if($serviceRequestHistorys != null){
            foreach ($service_assigns as $service_assign) {
                if($serviceRequestHistorys->serviceHistoryName == $service_assign->id){
                    $serviceRequestHistoryServiceName = $service_assign->serviceName;
                }
            }
            // หาจำนวนวันที่ผ่านไปตั้งแต่วันที่บันทึกประวัติการให้บริการจาก service_request_history
            $serviceRequestHistoryDateTime  = Carbon::parse($serviceRequestHistorys->serviceHistoryDateTime);
            $serviceRequestHistoryDateTimeUpdate  = Carbon::parse($serviceRequestHistorys->updated_at);
            $serviceRequestHistoryDayAmounts = $serviceRequestHistoryDateTime->diffInDays($serviceRequestHistoryDateTimeUpdate);
        }
        // หาจำนวนวันที่ผ่านไปตั้งแต่วันที่บันทึกประวัติการให้บริการจาก service_request
        $serviceDateTime  = Carbon::parse($serviceRequests->serviceDateTime);
        $updated_at  = Carbon::parse($serviceRequests->updated_at);
        $dayAmounts = $serviceDateTime->diffInDays($updated_at);

        if($dayAmounts <= 1){
            $priorityName = 'ด่วนที่สุด';
        }elseif($dayAmounts <= 3){
            $priorityName = 'ด่วนมาก';
        }elseif($dayAmounts <= 7){
            $priorityName = 'ด่วน';
        }elseif($dayAmounts <= 14){
            $priorityName = 'ปกติ';
        }elseif($dayAmounts <= 30){
            $priorityName = 'ปกติ';
        }elseif($dayAmounts <= 60){
            $priorityName = 'ปกติ';
        }

        if($serviceRequestHistoryDayAmounts <= 1){
            $serviceRequestHistoryDayAmountsPriorityName = 'ด่วนที่สุด';
        }elseif($serviceRequestHistoryDayAmounts <= 3){
            $serviceRequestHistoryDayAmountsPriorityName = 'ด่วนมาก';
        }elseif($serviceRequestHistoryDayAmounts <= 7){
            $serviceRequestHistoryDayAmountsPriorityName = 'ด่วน';
        }elseif($serviceRequestHistoryDayAmounts <= 14){
            $serviceRequestHistoryDayAmountsPriorityName = 'ปกติ';
        }elseif($serviceRequestHistoryDayAmounts <= 30){
            $serviceRequestHistoryDayAmountsPriorityName = 'ปกติ';
        }elseif($serviceRequestHistoryDayAmounts <= 60){
            $serviceRequestHistoryDayAmountsPriorityName = 'ปกติ';
        }

        if($serviceRequest == null){
            // $serviceRequestId = $serviceRequest->id + 1;
            $serviceRequestId = $id + 1;
        }

        $requestId = $serviceRequest->serviceRequestNumber;

		return view('admin.service_requests.view',compact('departments','service_assigns','requestId','prioritys','users','servicesStatus','serviceRequests','chatTitle','serviceRequestHistory','ServiceRequestMessages','serviceRequestHistoryServiceName','priorityName','serviceRequestHistoryDayAmountsPriorityName','fullName','serviceProviders'));
	}

	public function list(){
		// ตรวจสอบสิทธิ์การเข้าถึง
		$roles = Auth::user()->roles;
		foreach ($roles as $role) {
			if($role->title == 'Admin'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				$serviceRequest = ServiceRequest::get();
			}
			if($role->title == 'Staff'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
                $serviceRequest = ServiceRequest::whereIn('id', function($query) {
                    $query->select('reqid')
                        ->from('service_provider')
                        ->where('serviceProvider', Auth::user()->username);
                })->get();
			}
			if($role->title == 'User'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				$serviceRequest = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
			}
			if($role->title == 'Executive'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				$serviceRequest = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
			}
		}
		return response()->json(['status' => true, 'data' => $serviceRequest ]);
	}

	public function save(Request $request, $id = ""){

		$validator = Validator::make($request->all(), [
			'serviceRequestNumber' => 'required',
			'serviceName' => 'required',
			'serviceDepartment' => 'required',
			'serviceRecipient' => 'required',
		]);

		if($validator->fails()){
			return response()->json(['status' => false, 'error' => $validator->errors() ]);
		}else{
			$departmentName = Department::where('id','=',$request->input('serviceDepartment'))->orderBy('id')->first();
			// $priorityName = Priority::where('priorityStatus','=',$request->input('servicePriority'))->orderBy('id')->first();
			$serviceName = DB::table('service_assign')->where('id','=',$request->input('serviceName'))->orderBy('id')->first();


			if($request->file('serviceFileUpload') != null){
				$file = $request->file('serviceFileUpload');
				// Check if multiple files are uploaded
				if (is_array($file)) {
					// Loop through each file and get the original name
					$fileNames = [];
					foreach ($file as $singleFile) {
						$fileName = $singleFile->getClientOriginalName();
						$fileNames[] = $singleFile->getClientOriginalName();
						// $singleFile->storeAs('public/uploads', $fileName);
						$singleFile->move(public_path('uploads'), $fileName);
					}
					// Join file names with commas if you want a single string
					$fileName = implode(',', $fileNames);
				} else {
					// Single file upload
					$fileName = $file->getClientOriginalName();
				}
			} else {
				$fileName = '-';
			}

			$serviceDepartment = $request->input('serviceDepartment');
			$departmentType = $request->input('departmentType');

			if($serviceDepartment == 'etc'){
				$departments = Department::updateOrCreate(
					[
						'id' => $request->input('departmentId'),
					],
					[
						'departmentName' => $request->input('departmentName'),
						'departmentType' => $request->input('departmentType'),
				]);

				$departmentName = Department::orderBy('id','desc')->first(); // ดึงหน่วยงานที่เพิ่มใหม่
				$departmentName->id; // หน่วยงานที่ให้บริการใหม่
				$departmentType; //// หน่วยงานภายในหรือภายนอกใหม่

				if($departments){ // ถ้าเพิ่มหน่วยงานใหม่สำเร็จ
					// บันทึกคำขอใช้บริการ
					$serviceRequest = ServiceRequest::updateOrCreate(
						[
							'id' => $request->input('id'),
						],
						[
							'serviceRequestNumber' => $request->input('serviceRequestNumber'), // หมายเลขคำขอ
							'serviceDescription' => $request->input('serviceDescription'), // รายละเอียดคำขอ
							'serviceName' => $request->input('serviceName'), // ชื่องานบริการ
							'serviceDepartment' => $departmentName->id, // หน่วยงานที่ให้บริการ
							'serviceDepartmentType' => $departmentType, // หน่วยงานภายในหรือภายนอก
							'serviceFileUpload' => $fileName, // ไฟล์แนบ
							'serviceStatus' => 'pending', // สถานะคำขอ
							'serviceDateTime' => Carbon::now(),
							'servicePriority' => null, // ชั้นความเร็ว
							'serviceRecipient' => $request->input('serviceRecipient'), // ผู้ใช้บริการ
							'serviceProvider' => null, // ผู้ให้บริการ
					]);
				}
			} else {
                $serviceProviderCount   = count($request->input('serviceProvider'));
                $serviceProviderArray   = $request->input('serviceProvider');
                $serviceProvider        = $request->input('serviceProvider');

                $serviceRequest = ServiceRequest::updateOrCreate(
					[
						'id' => $request->input('id'),
					],
					[
						'serviceRequestNumber' => $request->input('serviceRequestNumber'),
						'serviceDescription' => $request->input('serviceDescription'),
						'serviceName' => $request->input('serviceName'),
						'serviceDepartment' => $request->input('serviceDepartment'),
						'serviceDepartmentType' => $departmentName->departmentType,
						'serviceFileUpload' => $fileName,
						'serviceStatus' => 'pending',
						'serviceDateTime' => Carbon::now(),
						'servicePriority' => null,
						'serviceRecipient' => $request->input('serviceRecipient'),
						'serviceProvider' => null,
				]);
                if($serviceRequest){
                    $reqid = ServiceRequest::orderBy('created_at','desc')->first();
                    if($serviceProviderCount == 1){
                            ServiceProvider::updateOrCreate(
                                [
                                    'id' => $request->input('id'),
                                ],
                                [
                                    'reqid' => $reqid->id,
                                    'serviceProvider' => $serviceProvider[0],
                            ]);
                    } else {
                        foreach ($serviceProviderArray as $provider) {
                            $serviceProvider = $provider;
                            ServiceProvider::updateOrCreate(
                                [
                                    'id' => $request->input('id'),
                                ],
                                [
                                    'reqid' => $reqid->id,
                                    'serviceProvider' => $serviceProvider,
                            ]);
                        }
                    }
                }
			}

			$name = Auth()->user()->name;
			$email = Auth()->user()->email;

			if($serviceRequest){ // ถ้าเพิ่มคำขอใช้บริการสำเร็จ
				$details = [
					'message'           => 'ระบบขอใช้บริการ',
					'to'                => $name,
					'serviceRequestNumber' => $request->input('serviceRequestNumber'),
					'serviceDescription' 	=> $request->input('serviceDescription'),
					'serviceName' 		=> $serviceName->serviceName,
					'serviceDepartment' => $departmentName->departmentName,
					'serviceDepartmentType' => $departmentName->departmentType,
					'serviceFileUpload' => $fileName,
					'serviceStatus' 	=> 'pending',
					'serviceDateTime' 	=> Carbon::now(),
					'servicePriority' 	=> null,
					'serviceRecipient' 	=> $request->input('serviceRecipient'),
					'serviceProvider' 	=> $serviceName->serviceProvider,
					'messageContact'    => 'หากมีข้อสงสัยเพิ่มเติม กรุณาติดต่อที่อีเมล',
					'mail'              => 'wittaya.kh@psu.ac.th',
					'regard'            => 'ขอแสดงความนับถือ',
					'itsupport'         => 'วิทยา ควรวิไลย',
					'position'          => 'นักวิชาการคอมพิวเตอร์',
					'workgroup'         => 'สาขาวิทยาศาสตร์การคำนวณ',
					'faucultySci'       => 'คณะวิทยาศาสตร์ มหาวิทยาลัยสงขลานครินทร์',
					'more'              => 'ดูรายละเอียดเพิ่มเติม',
					'tel'               => '093-639-8064',
					'cookie_policy'     => 'ข้อกำหนดการใช้งาน',
					'privacy_policy'    => 'นโยบายความเป็นส่วนตัว',
				];

				// ส่งอีเมลแจ้งเตือน
				// Mail::send(new SendEmail($details,$email,$fileName));

				return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
			}
		}
	}

    public function update(Request $request, $id = ""){

		$validator = Validator::make($request->all(), [
			'serviceRequestNumber' => 'required',
			'serviceName' => 'required',
			'serviceDepartment' => 'required',
			// 'servicePriority' => 'required',
			'serviceRecipient' => 'required',
		]);

		if($validator->fails()){
			return response()->json(['status' => false, 'error' => $validator->errors() ]);
		}else{
			$departmentName = Department::where('id','=',$request->input('serviceDepartment'))->orderBy('id')->first();
			// $priorityName = Priority::where('priorityStatus','=',$request->input('servicePriority'))->orderBy('id')->first();
			$serviceName = DB::table('service_assign')->where('id','=',$request->input('serviceName'))->orderBy('id')->first();


			if($request->file('serviceFileUpload') != null){
				$file = $request->file('serviceFileUpload');
				// Check if multiple files are uploaded
				if (is_array($file)) {
					// Loop through each file and get the original name
					$fileNames = [];
					foreach ($file as $singleFile) {
						$fileName = $singleFile->getClientOriginalName();
						$fileNames[] = $singleFile->getClientOriginalName();
						// $singleFile->storeAs('public/uploads', $fileName);
						$singleFile->move(public_path('uploads'), $fileName);
					}
					// Join file names with commas if you want a single string
					$fileName = implode(',', $fileNames);
				} else {
					// Single file upload
					$fileName = $file->getClientOriginalName();
				}
			} else {
				$fileName = '-';
			}

			$serviceDepartment = $request->input('serviceDepartment');
			$departmentType = $request->input('departmentType');

			if($serviceDepartment == 'etc'){
				$departments = Department::updateOrCreate(
					[
						'id' => $request->input('departmentId'),
					],
					[
						'departmentName' => $request->input('departmentName'),
						'departmentType' => $request->input('departmentType'),
				]);

				$departmentName = Department::orderBy('id','desc')->first(); // ดึงหน่วยงานที่เพิ่มใหม่
				$departmentName->id; // หน่วยงานที่ให้บริการใหม่
				$departmentType; //// หน่วยงานภายในหรือภายนอกใหม่

				if($departments){ // ถ้าเพิ่มหน่วยงานใหม่สำเร็จ
                    $serviceRequests = ServiceRequest::where('id','=',$request->input('id'))->get();
                    foreach ($serviceRequests as $serviceRequest) {
                        ServiceRequestHistory::create(
                            [
                                'reqid' => $serviceRequest->id,
                                'serviceRequestHistoryNumber' => $serviceRequest->serviceRequestNumber,
                                'serviceHistoryDescription' => $serviceRequest->serviceDescription,
                                'serviceHistoryName' => $serviceRequest->serviceName,
                                'serviceHistoryDepartment' => $serviceRequest->serviceDepartment,
                                'serviceHistoryDepartmentType' => $serviceRequest->serviceDepartmentType,
                                'serviceHistoryFileUpload' => $serviceRequest->serviceFileUpload,
                                'serviceHistoryStatus' => $serviceRequest->serviceStatus,
                                'serviceHistoryDateTime' =>$serviceRequest->serviceDateTime,
                                'serviceHistoryPriority' => null,
                                'serviceHistoryRecipient' => $serviceRequest->serviceRecipient,
                                'serviceHistoryProvider' => $serviceRequest->serviceProvider,
                        ]);
                    }
					// บันทึกคำขอใช้บริการ
					$serviceRequest = ServiceRequest::updateOrCreate(
						[
							'id' => $request->input('id'),
						],
						[
							'serviceRequestNumber' => $request->input('serviceRequestNumber'), // หมายเลขคำขอ
							'serviceDescription' => $request->input('serviceDescription'), // รายละเอียดคำขอ
							'serviceName' => $request->input('serviceName'), // ชื่องานบริการ
							'serviceDepartment' => $departmentName->id, // หน่วยงานที่ให้บริการ
							'serviceDepartmentType' => $departmentType, // หน่วยงานภายในหรือภายนอก
							'serviceFileUpload' => $fileName, // ไฟล์แนบ
							'serviceStatus' => 'pending', // สถานะคำขอ
							'serviceDateTime' => Carbon::now(),
							'servicePriority' => null, // ชั้นความเร็ว
							'serviceRecipient' => $request->input('serviceRecipient'), // ผู้ใช้บริการ
							// 'serviceProvider' => $serviceName->serviceProvider, // ผู้ให้บริการ
                            'serviceProvider' => null, // ผู้ให้บริการ
					]);
				}
			} else {
                $serviceRequests = ServiceRequest::where('id','=',$request->input('id'))->get();
                foreach ($serviceRequests as $serviceRequest) {
                    ServiceRequestHistory::create(
                        [
                            'reqid' => $serviceRequest->id,
                            'serviceRequestHistoryNumber' => $serviceRequest->serviceRequestNumber,
                            'serviceHistoryDescription' => $serviceRequest->serviceDescription,
                            'serviceHistoryName' => $serviceRequest->serviceName,
                            'serviceHistoryDepartment' => $serviceRequest->serviceDepartment,
                            'serviceHistoryDepartmentType' => $serviceRequest->serviceDepartmentType,
                            'serviceHistoryFileUpload' => $serviceRequest->serviceFileUpload,
                            'serviceHistoryStatus' => $serviceRequest->serviceStatus,
                            'serviceHistoryDateTime' =>$serviceRequest->serviceDateTime,
                            'serviceHistoryPriority' => null,
                            'serviceHistoryRecipient' => $serviceRequest->serviceRecipient,
                            // 'serviceHistoryProvider' => $serviceRequest->serviceProvider,
                            'serviceProvider' => null, // ผู้ให้บริการ
                    ]);
                }
				$serviceRequest = ServiceRequest::updateOrCreate(
					[
						'id' => $request->input('id'),
					],
					[
						'serviceRequestNumber' => $request->input('serviceRequestNumber'),
						'serviceDescription' => $request->input('serviceDescription'),
						'serviceName' => $request->input('serviceName'),
						'serviceDepartment' => $request->input('serviceDepartment'),
						'serviceDepartmentType' => $departmentName->departmentType,
						'serviceFileUpload' => $fileName,
						'serviceStatus' => 'pending',
						'serviceDateTime' => Carbon::now(),
						'servicePriority' => null,
						'serviceRecipient' => $request->input('serviceRecipient'),
						'serviceProvider' => $serviceName->serviceProvider,
				]);
			}

			$name = Auth()->user()->name;
			$email = Auth()->user()->email;

			if($serviceRequest){ // ถ้าเพิ่มคำขอใช้บริการสำเร็จ
				$details = [
					'message'           => 'ระบบขอใช้บริการ',
					'to'                => $name,
					'serviceRequestNumber' => $request->input('serviceRequestNumber'),
					'serviceDescription' 	=> $request->input('serviceDescription'),
					'serviceName' 		=> $serviceName->serviceName,
					'serviceDepartment' => $departmentName->departmentName,
					'serviceDepartmentType' => $departmentName->departmentType,
					'serviceFileUpload' => $fileName,
					'serviceStatus' 	=> 'pending',
					'serviceDateTime' 	=> Carbon::now(),
					'servicePriority' 	=> null,
					'serviceRecipient' 	=> $request->input('serviceRecipient'),
					'serviceProvider' 	=> $serviceName->serviceProvider,
					'messageContact'    => 'หากมีข้อสงสัยเพิ่มเติม กรุณาติดต่อที่อีเมล',
					'mail'              => 'wittaya.kh@psu.ac.th',
					'regard'            => 'ขอแสดงความนับถือ',
					'itsupport'         => 'วิทยา ควรวิไลย',
					'position'          => 'นักวิชาการคอมพิวเตอร์',
					'workgroup'         => 'สาขาวิทยาศาสตร์การคำนวณ',
					'faucultySci'       => 'คณะวิทยาศาสตร์ มหาวิทยาลัยสงขลานครินทร์',
					'more'              => 'ดูรายละเอียดเพิ่มเติม',
					'tel'               => '093-639-8064',
					'cookie_policy'     => 'ข้อกำหนดการใช้งาน',
					'privacy_policy'    => 'นโยบายความเป็นส่วนตัว',
				];

				// ส่งอีเมลแจ้งเตือน
				// Mail::send(new SendEmail($details,$email,$fileName));

				return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
			}
		}
	}

	public function selectdepartments(Request $request){
		$validator = Validator::make($request->all(), [
			'department' => 'required',
			'service' => 'required',
		]);

		if($validator->fails()){
			return response()->json(['status' => false, 'error' => $validator->errors() ]);
		}else{

			$service_id = $request->input('service');
			$assign_tasks = DB::table('assign_tasks')->get();
			$arr_username = [];
			foreach ($assign_tasks as $assign_tasks_item) {
				$usernames = $assign_tasks->filter(function ($item) use ($service_id){
					return in_array($service_id, explode(',', $item->service_id));
				})->pluck('username')->all();
				$arr_username = $usernames;
			}

			$department = $request->input('department');
			$department_id = DB::table('department')->where('department_name',$department)->first();
			$department_id = $department_id->id;
			$assigntasks = DB::table('assign_tasks')->get();

			foreach ($assigntasks as $assigntask) {
				$usernameDepartment = $assigntasks->filter(function ($item) use ($department_id){
					return in_array($department_id, explode(',', $item->department_id));
				})->pluck('username')->all();
			}

			// เช็คว่า username มีงานที่รับผิดชอบตรงกับหลักสูตรหรือไม่
			$users = DB::table('users')->where('username','=',$arr_username[0])->first();
			$data = [
				'status' => true,
				'message' => 'บันทึกสำเร็จ!',
				'data' => ['services' => $users->name],
			];

			return response()->json(['status' => true, 'data' => $data ]);
		}
	}

	public function find($id){
		$serviceRequest = ServiceRequest::findOrFail($id);

		return response()->json(['status' => true, 'data' => $serviceRequest ]);
	}

	public function delete($id){
		$serviceRequest = ServiceRequest::findOrFail($id);
		if($serviceRequest->delete()){
			return response()->json(['status' => true, 'message' => 'ลบสำเร็จ!' ]);
		}
	}

	public function download($id)
    {
		// Assuming you have the file path or file name based on $id
		// $filePath = storage_path("app/public/uploads/{$id}"); // Adjust path as needed
		$filePath = public_path("uploads/{$id}"); // Adjust path as needed
		// dd($filePath);
		if (!file_exists($filePath)) {
			abort(404, 'File not found');
		}

		// Dynamically get the MIME type
		$mimeType = mime_content_type($filePath);

		return Response::download($filePath, basename($filePath), [
			'Content-Type' => $mimeType,
		]);
    }

	public function service_status_change(Request $request)
    {
		$id = $request->input('dataId');

		$serviceRequest = ServiceRequest::where('id','=',$id )->orderBy('id')->first();
		// $priorityName = Priority::where('priorityStatus','=',$serviceRequest->servicePriority)->orderBy('id')->first();
		$serviceName = ServiceAssign::where('id','=',$serviceRequest->serviceName)->orderBy('id')->first();
		$departmentName = Department::where('id','=',$serviceRequest->serviceDepartment)->orderBy('id')->first();
		$serviceRecipient = User::where('username','=',$serviceRequest->serviceRecipient)->orderBy('id')->first();
		$serviceProvider = User::where('username','=',$serviceRequest->serviceProvider)->orderBy('id')->first();

		$serviceRequest->serviceRequestNumber; // หมายเลขคำขอ
		$serviceName->serviceName; // งานบริการที
		$serviceRequest->serviceDescription;
		// $priorityName->priorityName; // ชั้นความเร็ว
		$departmentName->departmentName; // หน่วยงานที่ให้บริการ
		$serviceRequest->serviceDateTime; // วันที่และเวลาที่คำขอใช้บริการ
		$serviceRecipient->name; // ผู้ขอใช้บริการ
		$serviceStatus = $request->input('serviceStatus'); // สถานะคำขอ

        $serviceRequests = ServiceRequest::where('id','=',$id)->get();
        foreach ($serviceRequests as $serviceRequest) {
            ServiceRequestHistory::create(
                [
                    'serviceRequestHistoryNumber' => $serviceRequest->serviceRequestNumber,
                    'serviceHistoryDescription' => $serviceRequest->serviceDescription,
                    'serviceHistoryName' => $serviceRequest->serviceName,
                    'serviceHistoryDepartment' => $serviceRequest->serviceDepartment,
                    'serviceHistoryDepartmentType' => $serviceRequest->serviceDepartmentType,
                    'serviceHistoryFileUpload' => $serviceRequest->serviceFileUpload,
                    'serviceHistoryStatus' => $serviceRequest->serviceStatus,
                    'serviceHistoryDateTime' => $serviceRequest->serviceDateTime,
                    'serviceHistoryPriority' => null,
                    'serviceHistoryRecipient' => $serviceRequest->serviceRecipient,
                    'serviceHistoryProvider' => $serviceRequest->serviceProvider,
            ]);
        }

		$serviceRequest = ServiceRequest::updateOrCreate(
			[
				'id' => $request->input('dataId'),
			],
			[
			'serviceStatus' => $request->input('serviceStatus'),
		]);

		$fileName = null;
		$name = Auth()->user()->name;
		$email = Auth()->user()->email;

		if ($serviceStatus == "completed") {
			if ($serviceRequest) { // ถ้าเพิ่มคำขอใช้บริการสำเร็จ
				$details = [
					'message' => 'ระบบขอใช้บริการ',
					'to' => $name,
					'serviceRequestNumber' => $serviceRequest->serviceRequestNumber,
					'serviceDescription' => $serviceRequest->serviceDescription,
					'serviceName' => $serviceName->serviceName,
					'serviceDepartment' => $departmentName->departmentName,
					'serviceDepartmentType' => $departmentName->departmentType,
					'serviceFileUpload' => $fileName,
					'serviceStatus' => "ดำเนินการเสร็จสิ้น",
					'serviceDateTime' => Carbon::now(),
					'servicePriority' => null,
					'serviceRecipient' => $serviceRecipient->name,
					'serviceProvider' => $serviceProvider->name,
					'messageContact' => 'หากมีข้อสงสัยเพิ่มเติม กรุณาติดต่อที่อีเมล',
					'mail' => 'wittaya.kh@psu.ac.th',
					'regard' => 'ขอแสดงความนับถือ',
					'itsupport' => 'วิทยา ควรวิไลย',
					'position' => 'นักวิชาการคอมพิวเตอร์',
					'workgroup' => 'สาขาวิทยาศาสตร์การคำนวณ',
					'faucultySci' => 'คณะวิทยาศาสตร์ มหาวิทยาลัยสงขลานครินทร์',
					'more' => 'ดูรายละเอียดเพิ่มเติม',
					'tel' => '093-639-8064',
					'cookie_policy' => 'ข้อกำหนดการใช้งาน',
					'privacy_policy' => 'นโยบายความเป็นส่วนตัว',
				];

				// ส่งอีเมลแจ้งเตือน
				// Mail::send(new SendEmail($details, $email, $fileName));

				return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
			}
		} else {
			return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
		}
    }

    public function serviceFlag(Request $request)
    {
		$id = $request->input('dataId');
        $flagValue = $request->input('flagValue');
        $serviceRequest = ServiceRequest::updateOrCreate(
            [
                'id' => $id,
            ],
            [
                'serviceFlag' => $flagValue,
        ]);
        if($serviceRequest){
            return response()->json(['status' => true, 'message' => 'ติดดาวสำเร็จ']);
        }
    }

	public function updateServiceRequestNote(Request $request)
	{
		$serviceRequestId = $request->input('serviceRequestId');
		$serviceRequestNumber = $request->input('serviceRequestNumber');
		$serviceRequestNote = $request->input('serviceRequestNote');
		$serviceRequest = ServiceRequest::where('serviceRequestNumber','=',$serviceRequestNumber)->first();
		$serviceRequestProvider = $serviceRequest->serviceProvider;

		$ServiceRequestNote = ServiceRequestNote::create(
			[
				'serviceRequestId' => $serviceRequestId,
				'serviceRequestNumber' => $serviceRequestNumber,
				'serviceRequestNote' => $serviceRequestNote,
				'serviceRequestProvider' => $serviceRequestProvider,
		]);

		if($ServiceRequestNote){
			return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ']);
		}
	}

    public function serviceRequestMessage(Request $request)
	{
        $id = $request->input('dataId');
		$serviceRequestMessage = $request->input('serviceRequestMessage');
        $serviceRequest = ServiceRequest::where('id','=',$id)->first();
        $serviceProviders = ServiceProvider::where('reqid','=',$id,'and')->where('serviceProvider','=',Auth::user()->username)->first();

        // ตรวจสอบสิทธิ์การเข้าถึง
		$roles = Auth::user()->roles;
		foreach ($roles as $role) {
			if($role->title == 'Admin'){
			}
			if($role->title == 'Staff'){
                // dd($serviceRequest->serviceProvider);
                $ServiceRequestNote = ServiceRequestMessage::create(
                    [
                        'reqid' => "$id",
                        'requestMessageServiceName'=> "$serviceRequest->serviceName",
                        'requestMessageSender'=> "$serviceProviders->serviceProvider",
                        'requestMessage'=> "$serviceRequestMessage",
                        'requestMessageDateTime'=> Carbon::now(),
                ]);
			}
			if($role->title == 'User'){
                // dd($serviceRequest->serviceRecipient);
                $ServiceRequestNote = ServiceRequestMessage::create(
                    [
                        'reqid' => "$id",
                        'requestMessageServiceName'=> "$serviceRequest->serviceName",
                        'requestMessageSender'=> "$serviceRequest->serviceRecipient",
                        'requestMessage'=> "$serviceRequestMessage",
                        'requestMessageDateTime'=> Carbon::now(),
                ]);
			}
			if($role->title == 'Executive'){

			}
		}

		return response()->json(['status' => true, 'message' => 'success']);
	}

	public function getTimeLine($id)
	{
		$serviceRequestNotes = ServiceRequestNote::join('service_request','service_request.id','=','service_request_note.serviceRequestId')
		->join('service_assign','service_assign.id','=','service_request.serviceName')
		->join('users','users.username','=','service_request.serviceProvider')
		->where('serviceRequestId','=',$id)->get();
		// dd($serviceRequestNotes);
		if($serviceRequestNotes){
			return response()->json(['status' => true, 'data' => $serviceRequestNotes ]);
		}else{
			return response()->json(['status' => false, 'message' => 'ไม่พบข้อมูล']);
		}
	}

	public function getNextOrderNumber()
	{
		// Get the last created order
		// $lastOrder = Order::orderBy('created_at', 'desc')->first();
		$lastOrder = ServiceAssign::orderBy('id')->first();

		// Set Prefix
		// $prefix = date('Y');
		$prefix = 'REQ' . date('dmY');

		// Set db-field
		$field = 'order_id';

		// Set length of incrementing number
		$length = 6;

		if (!$lastOrder) {
			// We get here if there is no order at all
			// If there is no number set it to 0, which will be 1 at the end.

			$number = 0;
		} else {
			// If we have ORD2023000001 in the database then we only want the number
			// So the substr returns this 000001
			$number = substr($lastOrder->{$field}, strlen($prefix));
		}

		// Reset incrementing no if prefix has changed (e.g. in new year)
		if (substr($lastOrder->order_no, 0, strlen($prefix)) !== $prefix) {
			$number = 0;
		}

		// Add the string in front and higher up the number.
		// the %05d part makes sure that there are always 6 numbers in the string.
		// so it adds the missing zero's when needed.

		return sprintf('%s%0' . $length . 'd', $prefix, intval($number) + 1);
	}


}
