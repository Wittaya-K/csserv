<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Illuminate\Support\Facades\Auth;

class PSUAuthController extends Controller
{
    // Redirect ไปยัง PSU OAuth2
    public function redirectToPSU()
    {
        return Socialite::driver('psu')->redirect();
    }

    // Callback จาก PSU OAuth2
    public function handlePSUCallback()
    {
        try {
            $user = Socialite::driver('psu')->user();

            // ค้นหาหรือสร้างบัญชีผู้ใช้ใหม่ในระบบของเรา
            $authUser = User::updateOrCreate([
                'email' => $user->getEmail(),
            ], [
                'name' => $user->getName(),
                'psu_id' => $user->getId(),
            ]);

            Auth::login($authUser, true);

            return redirect()->intended('/admin');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed.');
        }
    }
}
