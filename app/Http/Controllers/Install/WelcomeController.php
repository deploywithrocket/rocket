<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    public function show()
    {
        return inertia('install/welcome/show');
    }
}
