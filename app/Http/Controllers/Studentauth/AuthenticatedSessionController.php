<?php

namespace App\Http\Controllers\StudentAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Studentauth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('student.auth.login');
    }

    public function store(LoginRequest $request)
    {
        Auth::guard('employer')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('university')->logout();

        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::STUDENT_HOME);
    }

    public function destroy(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/student/login');
    }
}
