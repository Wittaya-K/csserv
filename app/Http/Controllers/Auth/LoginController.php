<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Carbon;
use Throwable;
use Adldap\Laravel\Facades\Adldap;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        // $username = $request->input('username'); //ชื่อผู้ใช้
        // $password = $request->input('password'); //รหัสผ่าน

        // $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        // if (auth()->attempt(array($fieldType => $username, 'password' => $request->input('password')))) {
        //     return redirect('/admin');
        // } else {
        //     return redirect()->to('/login')->with('error', 'Username หรือ Password ไม่ถูกต้อง กรุณา Login ใหม่อีกครั้ง!');
        // }

        try {
            $username = $request->input('username'); //ชื่อผู้ใช้
            $password = $request->input('password'); //รหัสผ่าน
            // $username = 'tanonchai.r';
            // Finding a user:
            $ldapUser =  Adldap::search()->users()->where('userprincipalname', '=', $username."@psu.ac.th")->first();
            // $ldapUser = Adldap::search()->users()->find('athasit.ro');
            // $ldapUser = Adldap::search()->users()->find($username);
            // dd($ldapUser);

            if ($ldapUser != null) {
                // Get all attributes
                // $attributes = $ldapUser->getAttributes();
                // Print all attributes
                // var_dump($attributes);
                // var_dump($ldapUser);
                // 0 => "objectclass"       "0042811-wittaya"
                // 1 => "cn"
                // 2 => "sn"                "KHUANWILAI"
                // 3 => "title"             "นักวิชาการอุดมศึกษา"
                // 4 => "description"       "วิทยา ควรวิไลย"
                // 5 => "givenname"         "WITTAYA"
                // 6 => "initials"
                // 7 => "distinguishedname" "CN=0042811-wittaya,OU=D822,OU=F08,OU=C01,OU=Staffs,DC=psu,DC=ac,DC=th"
                // 8 => "department"        "สำนักงานบริหารคณะ"
                // 9 => "company"           "วิทยาเขตหาดใหญ่"
                // 10 => "name"             "0042811-wittaya"
                // 11 => "objectguid"
                // 12 => "userprincipalname" "wittaya.kh@psu.ac.th"
                // 13 => "objectcategory"   "CN=Person,CN=Schema,CN=Configuration,DC=psu,DC=ac,DC=th"
                // 14 => "mail"             "wittaya.kh@psu.ac.th"
                // $distinguishedName = $ldapUser->getAttribute('distinguishedname')[0];   //กลุ่มผู้ใช้งาน
                // $fullnameTH        = $ldapUser->getAttribute('description')[0];         //ชื่อภาษาไทย
                // $givenname         = $ldapUser->getAttribute('givenname')[0];           // ชื่อ ENG
                // $sn                = $ldapUser->getAttribute('sn')[0];                  // สกุล ENG
                // $email             = $ldapUser->getAttribute('userprincipalname')[0];   // PSU Mail

                $title = $ldapUser->getAttribute('title')[0];                              // ตำแหน่งทางวิชาการ
                $department = $ldapUser->getAttribute('department')[0];                    // ส่วนงาน
                $distinguishedName = $ldapUser->getAttribute('distinguishedname')[0];      //กลุ่มผู้ใช้งาน
                $fullnameTH = $ldapUser->getAttribute('description')[0];                   //ชื่อภาษาไทย
                $email = $ldapUser->getAttribute('userprincipalname')[0];                  // PSU Mail

                $staffs = explode(",", $distinguishedName);
                $ou_f08 = $staffs[2]; // คณะวิทยาศาสตร์
                if ($ou_f08 == "OU=F08") {
                    foreach ($staffs as $staff) {
                        if ($staff == "OU=Staffs") { //บุลลากร
                                // $username = $email;
                                $password = $request->input('password');
                                $users = DB::table('users')->where('username', '=', $username)->count() === 0;
                                if ($users == true) {
                                    $userAttribues = [
                                        'username' => $username,
                                    ];
                                    // เพิ่มผู้ใช้งาน
                                    $addUsers = [
                                        'name' => $fullnameTH,
                                        'email' => $email, //psu email,
                                        'email_verified_at' => null,
                                        'username' => $username, //username
                                        'password' => Hash::make($password), //สร้างรหัสผ่าน
                                        'remember_token' => null,
                                        'department_name' => $title,
                                        'created_at' => Carbon::now()->timezone('Asia/Bangkok'), //วันที่สร้าง timezone Asia/Bangkok
                                        'updated_at' => Carbon::now()->timezone('Asia/Bangkok'), //วันที่อัปเดต timezone Asia/Bangkok
                                    ];
                                    DB::table("users")->updateOrInsert($userAttribues, $addUsers);

                                    $roleUser = DB::table('users')->where('username', '=', $username)->first();
                                    $addRoleUser = [
                                        'user_id' => $roleUser->id,
                                        'role_id' => "2",
                                    ];
                                    DB::table("role_user")->updateOrInsert($addRoleUser);
                                } else {
                                    $userAttribues = [
                                        'username' => $username,
                                    ];
                                    $addUsers = [
                                        'password' => Hash::make($password),
                                        'department_name' => $title,
                                    ];
                                    DB::table("users")->updateOrInsert($userAttribues, $addUsers);
                                }

                            $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
                            if (auth()->attempt(array($fieldType => $username, 'password' => $request->input('password')))) {
                                return redirect('/admin');
                            } else {
                                return redirect()->to('/login')->with('error', 'Username หรือ Password ไม่ถูกต้อง กรุณา Login ใหม่อีกครั้ง!');
                            }
                        } // end staffs
                    } // end foreach
                }
            }


        //DC1(VM),2(RACK),7(VM)-Hatyai,DC3(RACK)-Pattani,DC5(RACK)-Surat,DC6(RACK)-Trang
        $server = array("dc2.psu.ac.th","dc7.psu.ac.th","dc1.psu.ac.th");
        $basedn = "dc=psu,dc=ac,dc=th";
        $domain = "psu.ac.th";
        $username = $_POST['username'];
        $password = $_POST['password'];
        //Call function authentication
        $ldap = $this->authenticate($server,$basedn,$domain,$username,$password);
        if($ldap[0]){
            echo "<br/>>> User Profile <<<br/>";
            echo "Account Name : ".$ldap[1]['accountname']."<br/>";
            echo "Employee ID/Student ID : ".$ldap[1]['personid']."<br/>";
            echo "Citizen ID : ".$ldap[1]['citizenid']."<br/>";
            echo "CN : ".$ldap[1]['cn']."<br/>";
            echo "DN : ".$ldap[1]['dn']."<br/>";
            echo "Campus : ".$ldap[1]['campus']."(".$ldap[1]['campusid'].")<br/>";
            echo "Department : ".$ldap[1]['department']."(".$ldap[1]['departmentid'].")<br/>";
            echo "Work Detail : ".$ldap[1]['workdetail']."<br/>";
            echo "Position ID : ".$ldap[1]['positionid']."<br/>";
            echo "Description : ".$ldap[1]['description']."<br/>";
            echo "Display Name : ".$ldap[1]['displayname']."<br/>";
            echo "Detail : ".$ldap[1]['detail']."<br/>";
            echo "Title Name : ".$ldap[1]['title']."(".$ldap[1]['titleid'].")<br/>";
            echo "First Name : ".$ldap[1]['firstname']."<br/>";
            echo "Last Name : ".$ldap[1]['lastname']."<br/>";
            echo "Sex : ".$ldap[1]['sex']."<br/>";
            echo "Mail : ".$ldap[1]['mail']."<br/>";
            echo "Other Mail : ".$ldap[1]['othermail']."<br/>";
        }else{
            echo "This area is restricted.<br>";
            echo "Please login to continue.<br>";
        }

        } catch (Throwable $th) {
            return redirect()->to('/login')->with('error', 'ไม่มีสิทธิ์เข้าใช้งาน');
        }
    }

    public function redirectToPSU()
    {
        return Socialite::driver('psu')->redirect();
    }

    /**
     * Handle Callback จาก PSU OAuth2
     */
    public function handlePSUCallback()
    {
        try {
            $user = Socialite::driver('psu')->user();

            // ตรวจสอบว่าผู้ใช้มีอยู่ในระบบหรือยัง
            $authUser = User::where('email', $user->email)->first();

            if (!$authUser) {
                // ถ้ายังไม่มี ให้สร้าง User ใหม่
                $authUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username ?? null,
                    'password' => bcrypt(str()->random(16)), // กำหนดรหัสผ่านสุ่ม
                ]);
            }

            // ทำการล็อกอินผู้ใช้
            Auth::login($authUser, true);

            return redirect()->route('/admin'); // หรือเปลี่ยนเส้นทางไปหน้า Dashboard
        } catch (\Exception $e) {
            return redirect()->route('auth.login')->with('error', 'ไม่สามารถเข้าสู่ระบบได้');
        }
    }

    protected static function authenticate($server, $basedn, $domain, $username, $password)
    {
        $auth_status = false;
        $i = 0;
        while (($i < count($server)) && ($auth_status == false)) {
            $ldap = ldap_connect("ldaps://" . $server[$i]) or
                $auth_status = false;
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            $ldapbind = ldap_bind($ldap, $username . "@" . $domain, $password);
            if ($ldapbind) {
                if (empty($password)) {
                    $auth_status = false;
                } else {
                    $result[0] = true;
                    //Get User Info
                    $result[1] = self::get_user_info($ldap, $basedn, $username);
                }
            } else {
                $result[0] = false;
            }
            ldap_close($ldap);
            $i++;
        }
        return $result;
    }

    protected static function get_user_info($ldap, $basedn, $username)
    {
        $user['cn'] = "";
        $user['dn'] = "";
        $user['accountname'] = "";
        $user['personid'] = "";
        $user['citizenid'] = "";
        $user['campus'] = "";
        $user['campusid'] = "";
        $user['department'] = "";
        $user['departmentid'] = "";
        $user['workdetail'] = "";
        $user['positionid'] = "";
        $user['description'] = "";
        $user['displayname'] = "";
        $user['detail'] = "";
        $user['title'] = "";
        $user['titleid'] = "";
        $user['firstname'] = "";
        $user['lastname'] = "";
        $user['sex'] = "";
        $user['mail'] = "";
        $user['othermail'] = "";
        $sr = ldap_search(
            $ldap,
            $basedn,
            "(&(objectClass=user)(objectCategory=person)(sAMAccountName=" . $username . "))",
            array(
                "cn",
                "dn",
                "samaccountname",
                "employeeid",
                "citizenid",
                "company",
                "campusid",
                "department",
                "departmentid",
                "physicaldeliveryofficename",
                "positionid",
                "description",
                "displayname",
                "title",
                "personaltitle",
                "personaltitleid",
                "givenname",
                "sn",
                "sex",
                "userprincipalname",
                "mail"
            )
        );
        $info = ldap_get_entries($ldap, $sr);

        $user['cn'] = $info[0]["cn"][0];
        $user['dn'] = $info[0]["dn"];
        $user['accountname'] = $info[0]["samaccountname"][0];
        $user['personid'] = $info[0]["employeeid"][0];
        $user['citizenid'] = $info[0]["citizenid"][0];
        $user['campus'] = $info[0]["company"][0];
        $user['campusid'] = $info[0]["campusid"][0];
        $user['department'] = $info[0]["department"][0];
        $user['departmentid'] = $info[0]["departmentid"][0];
        $user['workdetail'] = $info[0]["physicaldeliveryofficename"][0];
        $user['positionid'] = $info[0]["positionid"][0];
        $user['description'] = $info[0]["description"][0];
        $user['displayname'] = $info[0]["displayname"][0];
        $user['detail'] = $info[0]["title"][0];
        $user['title'] = $info[0]["personaltitle"][0];
        $user['titleid'] = $info[0]["personaltitleid"][0];
        $user['firstname'] = $info[0]["givenname"][0];
        $user['lastname'] = $info[0]["sn"][0];
        $user['sex'] = $info[0]["sex"][0];
        $user['mail'] = $info[0]["userprincipalname"][0];
        $user['othermail'] = $info[0]["mail"][0];
        return $user;
    }
}
