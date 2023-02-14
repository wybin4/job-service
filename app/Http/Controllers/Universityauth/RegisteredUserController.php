<?php

namespace App\Http\Controllers\Universityauth;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('university.auth.register');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:universities'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = University::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::guard('university')->login($user);

        return redirect(RouteServiceProvider::UNIVERSITY_HOME);
    }
}
