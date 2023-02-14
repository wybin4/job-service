<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Interaction;
use App\Models\Profession;
use App\Models\Vacancy;
use App\Notifications\EmployerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentResponsesController extends Controller
{
    public function totalView(){
        return view("student.employer-interactions");
    }
    public function postResponse(Request $request)
    {
        Interaction::create([
            'vacancy_id' => $request->vacancy_id,
            'student_id' => Auth::user()->id,
            'status' => 0,
            'type' => 0
        ]);
        $vacancy = Vacancy::find($request->vacancy_id);
        Employer::find($vacancy->employer_id)->notify(new EmployerNotification(5, Auth::user()->id, $request->vacancy_id));
    }
    public function myResponses(){
        $interactions = Interaction::where('student_id', Auth::user()->id)->where('interactions.type', 0)->get();
        $employers = Employer::all();
        $vacancies = Vacancy::where('status', 0)->get();
        $professions = Profession::all();
        return view("student.my-responses", compact('interactions', 'employers', 'vacancies', 'professions'));
    }
}
