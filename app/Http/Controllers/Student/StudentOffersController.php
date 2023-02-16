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
use Illuminate\Support\Facades\DB;

class StudentOffersController extends Controller
{
    public function allOffers()
    {
        $interactions = DB::table('vacancies')
            ->join('interactions', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('employers', 'employers.id', '=', 'vacancies.employer_id')
            ->where('student_id', Auth::user()->id)
            ->where('interactions.type', 1)
            ->select(
                '*',
                'vacancies.profession_id as vacancy_profession_id',
                'interactions.id as employer_offer_id',
                'interactions.status as employer_offer_status',
                'interactions.created_at as employer_offer_created_at'
            )
            ->get();
        $employers = Employer::all();
        $vacancies = Vacancy::where('status', 0)->get();
        $professions = Profession::all();
        $interactions_all = Interaction::where('student_id', Auth::user()->id)->where('interactions.type', 1)->get();

        return view("student.all-offers", compact('interactions', 'employers', 'vacancies', 'professions', 'interactions_all'));
    }

    public function changeStatus(Request $request)
    {
        $sr = Interaction::find($request->id);
        $vacancy = Vacancy::find($sr->vacancy_id);
        Employer::find($vacancy->employer_id)->notify(new EmployerNotification($request->status, Auth::user()->id, $sr->vacancy_id));

        $sr->status = $request->status;
        $sr->save();
        return redirect()->back()->with('title', $request->title)->with('text', $request->text);
    }
}
