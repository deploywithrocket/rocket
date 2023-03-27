<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        $validator = validator()->make($request->input(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $validator->errors()->add('password', 'These credentials do not match our records.');

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput($request->input());
        }

        return redirect()->intended();
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('home');
    }
}
