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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\JobStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RequestListController extends Controller
{
	public function index(){

        $departments = Department::orderBy('id')->get();
        $service_assigns = ServiceAssign::orderBy('id')->get();
		$prioritys = Priority::orderBy('id')->get();
		$users = DB::table('users')->where('username','!=',NULL)->get();
		$servicesStatus = ServiceStatus::orderBy('id')->get();
		$serviceRequestNotes = ServiceRequestNote::orderBy('id')->get(); 
		// $requestId = $this->getNextOrderNumber();
		// $today = date("Ymd");

		$today = date("dmY");
		$rand = sprintf("%04d", rand(0,9999));
		$requestId = $today . $rand;

		return view('admin.service_lists.index',compact('departments','service_assigns','requestId','prioritys','users','servicesStatus','serviceRequestNotes'));
	}

	public function list(){
		// $serviceRequest = ServiceRequest::where('serviceProvider','=',Auth::user()->username)->get();
		$roles = Auth::user()->roles;
		foreach ($roles as $role) {
			if($role->title == 'Admin'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				// $serviceRequest = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
				$serviceRequest = ServiceRequest::get();
			}
			if($role->title == 'Staff'){
				// ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงคำขอที่เป็นของตนเอง
				// $serviceRequest = ServiceRequest::where('serviceRecipient','=',Auth::user()->username)->get();
				$serviceRequest = ServiceRequest::get();
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
			'servicePriority' => 'required',
			'serviceRecipient' => 'required',
		]);

		// dd($request->all());

		if($validator->fails()){
			return response()->json(['status' => false, 'error' => $validator->errors() ]);
		}else{
			$departmentName = Department::where('id','=',$request->input('serviceDepartment'))->orderBy('id')->first();
			// $serviceName = ServiceAssign::where('id','=',$request->input('serviceName'))->orderBy('id')->first();
			$priorityName = Priority::where('priorityStatus','=',$request->input('servicePriority'))->orderBy('id')->first();
			$serviceName = DB::table('service_assign')->where('id','=',$request->input('serviceName'))->orderBy('id')->first();

			if($request->file('file') != null){
				$file = $request->file('file');
				// Check if multiple files are uploaded
				if (is_array($file)) {
					// Loop through each file and get the original name
					$file_names = [];
					foreach ($file as $singleFile) {
						$fileName = $singleFile->getClientOriginalName();
						$file_names[] = $singleFile->getClientOriginalName();
						// $singleFile->storeAs('public/uploads', $fileName);
						$singleFile->move(public_path('uploads'), $fileName);
					}
					// Join file names with commas if you want a single string
					$file_name = implode(',', $file_names);
				} else {
					// Single file upload
					$file_name = $file->getClientOriginalName();
				}
			} else {
				$file_name = '-';
			}

			$schedule = ServiceRequest::updateOrCreate(
				[
					'id' => $request->input('id'),
				],
				[
					'serviceRequestNumber' => $request->input('serviceRequestNumber'),
					'serviceDescription' => $request->input('serviceDescription'),
					'serviceName' => $request->input('serviceName'),
					'serviceDepartment' => $request->input('serviceDepartment'),
					'serviceDepartmentType' => $departmentName->departmentType,
					'serviceFileUpload' => $file_name,
					'serviceStatus' => 'InProgress',
					'serviceDateTime' => Carbon::now(),
					'servicePriority' => $priorityName->priorityStatus,
					'serviceRecipient' => $request->input('serviceRecipient'),
					'serviceProvider' => $serviceName->serviceProvider,
			]);

			return response()->json(['status' => true, 'message' => 'บันทึกสำเร็จ!']);
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
		$schedule = ServiceRequest::findOrFail($id);
		// $schedule = DB::table('service_request')->where('id','=',$id)->first();
		return response()->json(['status' => true, 'data' => $schedule ]);
	}

	public function delete($id){
		$schedule = ServiceRequest::findOrFail($id);
		if($schedule->delete()){
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

	public function job_status(Request $request)
    {
		
		$job_status = $request->input('job_status');
		$id = $request->input('dataId');
		$schedule = ServiceRequest::updateOrCreate(
			[
				'id' => $request->input('dataId'),
			],
			[
			'job_status' => $request->input('job_status'),
		]);

		if($job_status == 'Received'){
			$message = 'รับเรื่องแล้ว';
		}
		if($job_status == 'InProcess'){
			$message = 'กำลังดำเนินการ';
		}
		if($job_status == 'Completed'){
			$message = 'เสร็จสิ้น';
		}

		$db_schedule = ServiceRequest::where('id',$id)->first();
		$emails = DB::table('users')->where('username',$db_schedule->user_service_required)->first();
		
		$email = $emails->email;
		$name = $emails->name;
		$title = $db_schedule->title;
		$description = $db_schedule->description;
		$file_name = $db_schedule->file;
		$department = $db_schedule->department;
		$schedule_from = $db_schedule->schedule_from;
		$schedule_to = $db_schedule->schedule_to;
		$filenames = explode(',',$file_name);
		$service_name = DB::table('service_request')->where('id','=',$db_schedule->service)->first();

		$scheduleFromDateTimeFormat = Carbon::parse($schedule_from)->format('d-m-Y H:i');
		$scheduleToDateTimeFormat = Carbon::parse($schedule_to)->format('d-m-Y H:i');

		if($schedule){
			//After job logic is complete, send the email
			$details = [
				'message'           => 'ระบบแจ้งขอใช้บริการ  '. $message,
				'messageSuccess'    => $message,
				'jobStatus'         => $job_status,
				'to'                => $name,
				'title'             => $title,
				'description'		=> $description,
				'file' 				=> 'uploads/'. $file_name,
				'department' 		=> $department,
				'service' 			=> $service_name->service_request_name,
				'schedule_from'		=> $scheduleFromDateTimeFormat,
				'schedule_to'		=> $scheduleToDateTimeFormat,
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

			Mail::send(new JobStatus($details,$email,$filenames));

			return response()->json(['status' => true, 'message' => $message]);
		}
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