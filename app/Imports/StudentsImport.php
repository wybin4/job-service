<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendMail;

class StudentsImport implements ToModel
{
    public function model(array $row)
    {
        
        return new Student([
            'university_id' => Auth::guard('university')->id(),
            'student_fio'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make(Str::random(8)),
        ]);
    }
}
