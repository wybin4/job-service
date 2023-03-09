<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Interaction;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\Vacancy;
use App\Notifications\StudentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VacancyResponsesController extends Controller
{
    public function totalView()
    {
        return view("employer.student-interactions");
    }
    public function vacancyResponses($id)
    {
        $vacancy = Vacancy::find($id);
        if (!$vacancy) {
            $text = "Вакансия, которую Вы хотели посмотреть, не найдена";
            return view('error.employer-error-404', compact("text"));
        } else {
            $interactions = Interaction::where('vacancy_id', $id)
                ->join('students', 'students.id', '=', 'interactions.student_id')
                ->join('resumes', 'resumes.student_id', '=', 'students.id')
                ->where('resumes.status', 0)
                ->where('interactions.type', 0)
                ->select(
                    '*',
                    'students.id as student_id',
                    'resumes.profession_id as resume_profession_id',
                    'resumes.id as student_resume_id',
                    'interactions.id as student_response_id',
                    'interactions.status as student_response_status',
                    'interactions.created_at as student_response_created_at'
                )
                ->get();
            return view("employer.vacancy.one-vacancy-responses", compact('vacancy', 'interactions'));
        }
    }
    public function allVacancyResponses()
    {
        $interactions = Vacancy::where('employer_id', Auth::user()->id)
            ->join('interactions', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->join('resumes', 'resumes.student_id', '=', 'students.id')
            ->where('resumes.status', 0)
            ->where('interactions.type', 0)
            ->select(
                '*',
                'students.id as student_id',
                'vacancies.id as vacancy_id',
                'vacancies.profession_id as vacancy_profession_id',
                'resumes.profession_id as resume_profession_id',
                'resumes.id as student_resume_id',
                'interactions.id as student_response_id',
                'interactions.status as student_response_status',
                'interactions.created_at as student_response_created_at'
            )
            ->get();
        return view("employer.vacancy.all-vacancy-responses", compact('interactions'));
    }
    public function changeStatus(Request $request)
    {
        $sr = Interaction::find($request->id);
        if ($request->status != 9) {
            Student::find($sr->student_id)->notify(new StudentNotification($request->status, Auth::user()->id));
        }
        if ($request->status == 3) {
            $sr->hired_at = now();
        }
        $sr->status = $request->status;
        if ($request->interview_data) {
            $sr->data =  [
                'interview_data' => $request->interview_data
            ];
        }
        $sr->save();
        return redirect()->back()->with('title', $request->title)->with('text', $request->text);
    }
}
