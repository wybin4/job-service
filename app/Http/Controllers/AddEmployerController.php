<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployerLoginToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Employer;
use Illuminate\Support\Str;

class AddEmployerController extends Controller
{
    public function openAddOne()
    {
        return view('admin.add-one-employer');
    }
    public function showEmployerPasswordSetter()
    {
        return view('set-password');
    }
    public function setPassword(Request $request, $token)
    {
        $token = EmployerLoginToken::whereToken(hash('sha256', $token))->firstOrFail();
        //abort_unless($token->isValid(), 401);
        $token->consume();
        Auth::guard('employer')->login($token->employer);
        $employer_id = $token->employer_id();
        $employer = Employer::find($employer_id);
        $employer->update([
            'password' => Hash::make($request->password)
        ]);
        Auth::guard('student')->logout();
        Auth::guard('employer')->logout();
        Auth::guard('admin')->logout();
        return redirect('/');
    }
    public function addOneEmployer(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employers'],
        ]);
        $employer = Employer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(8))
        ]);
        $employer->sendLoginLink();

        return back()->with('title', 'Добавление работодателя')->with('text', 'Успешно добавили работодателя');    }
}
