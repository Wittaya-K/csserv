<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\PSUAuthController;
use App\Http\Controllers\Admin\DepartmentsController;
use App\Http\Controllers\Admin\ServiceAssignController;
use App\Http\Controllers\Admin\ServiceRequestController;
use App\Http\Controllers\Admin\ServiceStatusController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\PriorityController;
use App\Http\Controllers\Admin\RequestListController;
use App\Http\Controllers\Admin\ReportSumarryController;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Illuminate\Http\Request;

Route::redirect('/', '/login');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
Route::post('login', 'Auth\LoginController@login')->name('auth.login');
Route::post('logout', 'Auth\LoginController@logout')->name('auth.logout');

Route::get('auth/psu', [PSUAuthController::class, 'redirectToPSU'])->name('auth.psu');
Route::get('auth/callback', [PSUAuthController::class, 'handlePSUCallback']);

Route::redirect('/home', '/admin');

Auth::routes(['register' => false]);

Route::get('/auth/redirect', function () {
    return Socialite::driver('azure')->redirect();
});

// Route::get('/auth/callback', function () {
//     $azureUser = Socialite::driver('azure')->user();
//     $businessPhones = $azureUser->user['businessPhones'];
//     $displayName = $azureUser->user['displayName'];
//     $givenName = $azureUser->user['givenName'];
//     $jobTitle = $azureUser->user['jobTitle'];
//     $mail = $azureUser->user['mail'];
//     $mobilePhone = $azureUser->user['mobilePhone'];
//     $officeLocation = $azureUser->user['officeLocation'];
//     $preferredLanguage = $azureUser->user['preferredLanguage'];
//     $surname = $azureUser->user['surname'];
//     $userPrincipalName = $azureUser->user['userPrincipalName'];
//     $id = $azureUser->user['id'];
//     $email = $azureUser->attributes['email'];

//     // dd($email);
//     // dd($azureUser);
//     // ตัวอย่างดึง email
//     //   user [
//     //     "@odata.context" => "https://graph.microsoft.com/v1.0/$metadata#users/$entity"
//     //     "businessPhones" => []
//     //     "displayName" => "Wittaya Khuanwilai (วิทยา ควรวิไลย)"
//     //     "givenName" => "WITTAYA"
//     //     "jobTitle" => "นักวิชาการคอมพิวเตอร์"
//     //     "mail" => "wittaya.kh@psu.ac.th"
//     //     "mobilePhone" => null
//     //     "officeLocation" => "สาขาวิชาวิทยาศาสตร์การคำนวณ คณะวิทยาศาสตร์"
//     //     "preferredLanguage" => "en-us"
//     //     "surname" => "KHUANWILAI"
//     //     "userPrincipalName" => "wittaya.kh@psu.ac.th"
//     //     "id" => "741e91b3-cf47-492c-a39d-644e0c3e8c51"
//     //   ]
//     //   attributes [
//     //     "id" => "741e91b3-cf47-492c-a39d-644e0c3e8c51"
//     //     "nickname" => null
//     //     "name" => "Wittaya Khuanwilai (วิทยา ควรวิไลย)"
//     //     "email" => "wittaya.kh@psu.ac.th"
//     //     "principalName" => "wittaya.kh@psu.ac.th"
//     //     "mail" => "wittaya.kh@psu.ac.th"
//     //     "avatar" => null
//     //   ]

//     $user = User::updateOrCreate([
//         'email' => $azureUser->email,
//     ], [
//         'name' => $azureUser->displayName,
//         'email' => $azureUser->email,
//         'username' => explode('@', $azureUser->getEmail()) ?? null,
//         'department_name' => $azureUser->jobTitle,
//     ]);

//     Auth::login($user);
//     return redirect('/admin');
// });

// Route::get('/logout', function (Request $request) {
//     // (1) ดึง id_token ที่บันทึกตอน login (ถ้ามี)
//     $idToken = $request->session()->pull('azure_id_token');

//     // (2) ออกจากระบบ Laravel
//     Auth::logout();
//     $request->session()->invalidate();
//     $request->session()->regenerateToken();

//     // (3) เตรียม URL Logout ของ Microsoft
//     $tenant   = config('services.azure.tenant_id');  // หรือ 'common'
//     $base     = "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/logout";

//     // post_logout_redirect_uri ต้องถูก “ลงทะเบียน” ใน Azure → App > Authentication
//     $params = [
//         'post_logout_redirect_uri' => url('/'),
//         // ส่ง id_token_hint จะช่วย Azure รู้ว่า session ไหนต้องปิด (ไม่บังคับ)
//         'id_token_hint'            => $idToken,
//     ];
//     $logoutUrl = $base.'?'.http_build_query($params);

//     // (4) Redirect ผู้ใช้ไป Azure เพื่อลบ SSO คุกกี้
//     return redirect()->away($logoutUrl);
// });

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');
    Route::resource('departments', DepartmentsController::class);
    Route::resource('service_assigns',ServiceAssignController::class);
    Route::resource('prioritys', PriorityController::class);
    Route::resource('service_status', ServiceStatusController::class);
    // Route::resource('report_summary', ReportSumarryController::class);

    Route::group(['prefix' => 'report_summary', 'as' => 'report_summary.'], function () {
        Route::get('/', [ReportSumarryController::class, 'index'])->name('index');
        Route::POST('/search', [ReportSumarryController::class, 'search'])->name('search');
    });

    Route::get('callbacks', 'CallbacksController@callback')->name('callbacks.callback');

    Route::group(['prefix' => 'service_requests', 'as' => 'service_requests.'], function(){
        Route::controller(ServiceRequestController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('list', 'list')->name('list');
            Route::post('save/{id?}', 'save')->name('save');
            Route::get('find/{id?}', 'find')->name('find');
            Route::post('delete/{id?}', 'delete')->name('delete');
            Route::get('download/{id?}', 'download')->name('download');
            Route::post('service_status_change', 'service_status_change')->name('service_status_change');
            Route::get('selectdepartments/', 'selectdepartments')->name('selectdepartments');
            Route::post('updateServiceRequestNote', 'updateServiceRequestNote')->name(name: 'updateServiceRequestNote');
            Route::get('getTimeLine/{id?}', 'getTimeLine')->name('getTimeLine');
            Route::get('create', 'create')->name('create');
            Route::get('edit/{id?}', 'edit')->name('edit');
            Route::get('view/{id?}', 'view')->name('view');
            Route::post('update/{id?}', 'update')->name('update');
            Route::post('serviceFlag', 'serviceFlag')->name('serviceFlag');
            Route::post('serviceRequestMessage', 'serviceRequestMessage')->name('serviceRequestMessage');
        });
    });

    Route::group(['prefix' => 'service_lists', 'as' => 'service_lists.'], function(){
        Route::controller(RequestListController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('list', 'list')->name('list');
            Route::post('save/{id?}', 'save')->name('save');
            Route::get('find/{id?}', 'find')->name('find');
            Route::post('delete/{id?}', 'delete')->name('delete');
            Route::get('download/{id?}', 'download')->name('download');
            Route::post('job_status', 'job_status')->name('job_status');
            Route::get('selectdepartments/', 'selectdepartments')->name('selectdepartments');
            Route::get('getTimeLine/{id?}', 'getTimeLine')->name('getTimeLine');
        });
    });

    Route::group(['prefix' => 'search', 'as' => 'search.'], function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/search', [SearchController::class, 'search'])->name('search');
        Route::get('/serviceProviderSearch', [SearchController::class, 'serviceProviderSearch'])->name('serviceProviderSearch');
        Route::get('/serviceRequestSearch', [SearchController::class, 'serviceRequestSearch'])->name(name: 'serviceRequestSearch');
        Route::get('/serviceRequestYearSearch', [SearchController::class, 'serviceRequestYearSearch'])->name(name: 'serviceRequestYearSearch');
    });
});
