<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\ResumeSkillRate;
use App\Models\Review;
use App\Models\Student;
use App\Notifications\StudentNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function postStudentRate(Request $request)
    {
        $interaction = Interaction::where('student_id', $request->student_id)
            ->where('vacancy_id', $request->vacancy_id)
            ->where('status', 3)->get();
        if (count($interaction)) {
            $interaction = $interaction[0];
            if ($request->dismiss_student) {
                $interaction->status = 9;
                ////Student::find($interaction->student_id)->notify(new StudentNotification(9, Auth::user()->id));?????
                $interaction->save();
            } else {
                $interaction->status = 8;
                $interaction->save();
            }
            $student = Student::find($request->student_id);
            $student_skills = $student->resume()
                ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
                ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
                ->where('skill_type', 1)
                ->select('skill_id', 'skill_name', 'student_skills.id as student_skill_id')
                ->get();
            if ($request->skill_rate) {
                $i = 0;
                foreach ($request->skill_rate as $index) {
                    ResumeSkillRate::create([
                        'employer_id' => Auth::User()->id,
                        'student_skill_id' => $student_skills[$i]->student_skill_id,
                        'skill_rate' => $request->skill_rate[$i] ?? 0,
                    ]);
                    $i++;
                }
            }
            if ($request->description) {
                Review::create([
                    'entity_id' => $student->resume()->get()[0]->id,
                    'reviewer_id' => Auth::User()->id,
                    'type' => 0,
                    'text' => $request->description
                ]);
            }
        }
        return redirect(RouteServiceProvider::EMPLOYER_HOME);
    }
    public function studentRatePage(Request $request)
    {
        $student = Student::find($request->input('student_id'));
        $vacancy_id = $request->input('vacancy_id');
        $student_skills = $student->resume()
            ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
            ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
            ->where('skill_type', 1)
            ->select('skill_id', 'skill_name')
            ->get();
        if ($request->input('dismiss_student')) {
            $dismiss_student = $request->input('dismiss_student');
        } else $dismiss_student = false;
        return view("employer.resume.student-rate", compact('student', 'student_skills', 'vacancy_id', 'dismiss_student'));
    }
    public function studentRatePageEdit(Request $request)
    {
        $student = Student::find($request->input('student_id'));
        $vacancy_id = $request->input('vacancy_id');
        $student_skills = $student->resume()
            ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
            ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
            ->where('skill_type', 1)
            ->select('skill_id', 'skill_name')
            ->get();
        $rated_skills = $student->resume()
            ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
            ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
            ->where('skill_type', 1)
            ->select('student_skills.id')
            ->pluck('id')
            ->all();
        $need_skills = ResumeSkillRate::whereIn('student_skill_id', $rated_skills)
            ->join('student_skills', 'student_skills.id', '=', 'resume_skill_rates.student_skill_id')
            ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
            ->where('employer_id', Auth::user()->id)
            ->select('resume_skill_rates.skill_rate')
            ->get();
        $description = Review::where('entity_id', $student->resume()->first()->id)
            ->where('reviewer_id', Auth::user()->id)
            ->where('type', 0)
            ->select('text')
            ->first();
        return view("employer.resume.edit-student-rate", compact('student', 'student_skills', 'vacancy_id', 'need_skills', 'description'));
    }
    public function editStudentRate(Request $request)
    {
        $student = Student::find($request->student_id);
        $student_skills = $student->resume()
            ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
            ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
            ->where('skill_type', 1)
            ->select('student_skills.id as id')
            ->pluck('id')->all();
        $rates = ResumeSkillRate::whereIn('student_skill_id', $student_skills)
            ->where('employer_id', Auth::user()->id)
            ->get();
        $i = 0;
        foreach ($rates as $rate) {
            $rate->skill_rate = $request->skill_rate[$i];
            $rate->save();
            $i++;
        }
        $review = Review::where('entity_id', $student->resume()->first()->id)
            ->where('reviewer_id', Auth::user()->id)->first();
        $review->text = $request->description;
        $review->save();
        return redirect(RouteServiceProvider::EMPLOYER_HOME);
    }
}
