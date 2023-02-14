<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class Location extends Controller
{
    public function addStudentLocation(Request $request)
    {
        $student = Student::find(Auth::guard('student')->user()->id);
        $student->update([
            'location' => $request->location
        ]);
    }
    public function addEmployerLocation(Request $request)
    {
        $employer = Employer::find(Auth::guard('employer')->user()->id);
        $employer->update([
            'location' => $request->location
        ]);
    }
}
