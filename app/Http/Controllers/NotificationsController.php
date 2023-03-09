<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\Student;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function studentMarkAsRead(Request $request)
    {
        Student::find($request->student_id)->unreadNotifications->where('id', $request->id)->markAsRead();
    }
    public function employerMarkAsRead(Request $request)
    {
        Employer::find($request->employer_id)->unreadNotifications->where('id', $request->id)->markAsRead();
    }
}
