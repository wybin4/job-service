<?php

namespace App\Http\Controllers\EmployerAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employerauth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('employer.auth.login');
    }

    public function store(LoginRequest $request)
    {
        Auth::guard('student')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('university')->logout();

        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::EMPLOYER_HOME);
    }

    public function destroy(Request $request)
    {
        Auth::guard('employer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/employer/login');
    }
}
