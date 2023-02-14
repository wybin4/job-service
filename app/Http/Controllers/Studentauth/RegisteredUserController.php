<?php

namespace App\Http\Controllers\Studentauth;

use App\Http\Controllers\Controller;
use App\Models\Student;
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
        return view('student.auth.register');
    }
    public function store(Request $request)
    {
        $request->validate([
            'student_fio' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:students'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Student::create([
            'student_fio' => $request->student_fio,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::guard('student')->login($user);

        return redirect(RouteServiceProvider::STUDENT_HOME);
    }
}
