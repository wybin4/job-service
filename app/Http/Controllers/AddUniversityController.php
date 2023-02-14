<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UniversityLoginToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\University;
use Illuminate\Support\Str;

class AddUniversityController extends Controller
{
    public function openAddOne()
    {
        return view('admin.add-one-university');
    }
    public function showUniversityPasswordSetter()
    {
        return view('set-password');
    }
    public function setPassword(Request $request, $token)
    {
        $token = UniversityLoginToken::whereToken(hash('sha256', $token))->firstOrFail();
        //abort_unless($token->isValid(), 401);
        $token->consume();
        Auth::guard('university')->login($token->university);
        $university_id = $token->university_id();
        $university = University::find($university_id);
        $university->update([
            'password' => Hash::make($request->password)
        ]);
        Auth::guard('student')->logout();
        Auth::guard('employer')->logout();
        Auth::guard('admin')->logout();
        return redirect('/');
    }
    public function addOneUniversity(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:universities'],
        ]);
        $university = University::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(8))
        ]);
        $university->sendLoginLink();

        return back()->with('message', 'Вы успешно добавили образовательное учреждение');
    }
}
