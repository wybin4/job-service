<?php

namespace App\Http\Controllers\Universityauth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Universityauth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('university.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        Auth::guard('student')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('employer')->logout();

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::UNIVERSITY_HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('university')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/university/login');
    }
}
