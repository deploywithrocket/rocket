<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show()
    {
        return inertia('install/user/show');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        $user->save();

        return redirect()->route('install.github.show');
    }
}
