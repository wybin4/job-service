<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Models\StudentLoginToken;
use Illuminate\Support\Facades\URL;

class AddStudentController extends Controller
{
    public function openAddOne()
    {
        return view('university.add-one');
    }
    public function showStudentPasswordSetter()
    {
        return view('set-password');
    }
    public function setPassword(Request $request, $token)
    {
        $token = StudentLoginToken::whereToken(hash('sha256', $token))->firstOrFail();
        //abort_unless($token->isValid(), 401);
        $token->consume();
        Auth::guard('student')->login($token->student);
        $student_id = $token->student_id();
        $student = Student::find($student_id);
        $student->update([
            'password' => Hash::make($request->password)
        ]);
        Auth::guard('university')->logout();
        Auth::guard('employer')->logout();
        Auth::guard('admin')->logout();
        return redirect('/');
    }
    public function addOneStudent(Request $request)
    {
        $request->validate([
            'student_fio' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:students'],
        ]);
        $student = Student::create([
            'student_fio' => $request->student_fio,
            'university_id' => Auth::guard('university')->id(),
            'email' => $request->email,
            'password' => Hash::make(Str::random(8))
        ]);
        $student->sendLoginLink();

        return back()->with('message', 'Вы успешно добавили студента');
    }
    public function openAddMany()
    {
        return view('university.add-many');
    }

    public function addManyStudents(Request $request)
    {
        $this->validate($request, [
            'students-file' => 'required|file|mimes:xls,xlsx'
        ], [
            'students-file.required' => 'Загрузите файл, чтобы продолжить',
            'students-file.file' => 'Загрузите файл, чтобы продолжить',
            'students-file.mimes' => 'Файл должен быть формата xls или xlsx',
        ]);

        Excel::import(new StudentsImport, $request->file('students-file'));

        $arr = Excel::toArray(new StudentsImport, $request->file('students-file'))[0];
        $emails = array();
        foreach ($arr as $email) {
            $emails[] = $email[1];
        }
        $students = Student::whereIn('email', $emails)->get();
        foreach ($students as $student) {
            $student->sendLoginLink();
        }
        return back()->with('message', 'Вы успешно добавили студентов');
    }
}
