<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Mail\sendOfferLink;
use App\Models\Interaction;
use App\Models\Student;
use App\Models\Vacancy;
use App\Notifications\StudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VacancyOffersController extends Controller
{
    public function sendOffer(Request $request)
    {
        Interaction::create([
            'vacancy_id' => $request->vacancy_id,
            'student_id' => $request->student_id,
            'status' => 0,
            'type' => 1,
            'data' => [
                'interview_data' => $request->text
            ]
        ]);
        Validator::make($request->all(), [
            'topic' => 'required',
            'text' => 'required'
        ]);
        Student::find($request->student_id)->notify(new StudentNotification(6, Auth::user()->id));
        $this->sendOfferLink($request->vacancy_id, $request->student_email, $request->topic, $request->text);
        return redirect()->back()->with('title', 'Отправка оффера')->with('text', 'Оффер успешно отправлен');
    }
    public function sendOfferLink($vacancy_id, $email, $topic, $text)
    {
        Mail::to($email)->queue(new sendOfferLink($vacancy_id, $topic, $text));
    }
    public function vacancyOffers($id)
    {
        $vacancy = Vacancy::find($id);
        $interactions = Interaction::where('vacancy_id', $id)
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->join('resumes', 'resumes.student_id', '=', 'students.id')
            ->where('resumes.status', 0)
            ->where('interactions.type', 1)
            ->select(
                '*',
                'students.id as student_id',
                'resumes.profession_id as resume_profession_id',
                'resumes.id as student_resume_id',
                'interactions.id as student_offer_id',
                'interactions.status as student_offer_status',
                'interactions.created_at as student_offer_created_at'
            )
            ->get();
        return view("employer.vacancy.vacancy-offers", compact('vacancy', 'interactions'));
    }
    public function allOffers()
    {
        $interactions = Vacancy::where('employer_id', Auth::user()->id)
            ->join('interactions', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->join('resumes', 'resumes.student_id', '=', 'students.id')
            ->where('resumes.status', 0)
            ->where('interactions.type', 1)
            ->select(
                '*',
                'students.id as student_id',
                'vacancies.id as vacancy_id',
                'vacancies.profession_id as vacancy_profession_id',
                'resumes.profession_id as resume_profession_id',
                'resumes.id as student_resume_id',
                'interactions.id as student_offer_id',
                'interactions.status as student_offer_status',
                'interactions.created_at as student_offer_created_at'
            )
            ->get();
        return view("employer.vacancy.all-offers", compact('interactions'));
    }
}
