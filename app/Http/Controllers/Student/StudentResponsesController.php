<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\EmployerRate;
use App\Models\Interaction;
use App\Models\Profession;
use App\Models\Student;
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
        return redirect()->back()->with('title', 'Отправка отклика')->with('text', 'Отклик успешно отправлен');
    }
    public function myResponses(){
        $interactions = Interaction::where('student_id', Auth::user()->id)->where('interactions.type', 0)
        ->orderBy('created_at', 'desc')
        ->get();
        $employers = Employer::all();
        $vacancies = Vacancy::where('status', 0)->get();
        $professions = Profession::all();

        /** */
        $status = [3, 8, 9];
        $places_of_work = Interaction::where('student_id', Auth::user()->id)
            ->whereIn('interactions.status', $status)
            ->join('vacancies', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('employers', 'employers.id', '=', 'vacancies.employer_id');
        $vacancy_ids = Student::find(Auth::user()->id)->interaction->pluck('vacancy_id')->toArray();
        $employer_ids = Vacancy::find($vacancy_ids)->unique('employer_id')->pluck('employer_id')->toArray();
        $vacancies_with_rate = EmployerRate::whereIn('employer_id', $employer_ids)
            ->where('student_id', Auth::user()->id)
            ->get()
            ->unique('employer_id')
            ->pluck('employer_id')
            ->toArray();
        $vacancies_with_rate = Vacancy::whereIn('employer_id', $vacancies_with_rate)
            ->whereIn('vacancies.id', $vacancy_ids)
            ->pluck('vacancies.id')
            ->toArray();
        return view("student.my-responses", compact('interactions', 'employers', 'vacancies', 'professions', 'vacancies_with_rate'));
    }
}
