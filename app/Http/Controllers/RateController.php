<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\ResumeSkillRate;
use App\Models\Review;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
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
            $resume_id = $student->resume->id;
            if ($request->skill_rate) {
                $i = 0;
                foreach ($request->skill_rate as $index) {
                    ResumeSkillRate::create([
                        'employer_id' => Auth::User()->id,
                        'resume_id' => $resume_id,
                        'skill_id' => $request->skill_id[$i],
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
        return redirect(RouteServiceProvider::EMPLOYER_HOME)->with('title', 'Оценка работника')->with('text', 'Успешно оценили работника');
    }
    public function studentRatePage(Request $request)
    {
        $student = Student::find($request->input('student_id'));
        $vacancy_id = $request->input('vacancy_id');
        $student_skills = $student->resume()
            ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
            ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
            ->select('skill_id', 'skill_name', 'skill_type')
            ->get();
        if ($request->input('dismiss_student')) {
            $dismiss_student = $request->input('dismiss_student');
        } else $dismiss_student = false;
        $ss = $student
            ->resume()
            ->join('student_skills', 'student_skills.resume_id', '=', 'resumes.id')
            ->get()
            ->pluck('skill_id')
            ->toArray();
        $skill = Skill::whereNotIn('id', $ss)->get();
        return view("employer.resume.student-rate", compact('student', 'student_skills', 'vacancy_id', 'dismiss_student', 'skill'));
    }
    public function studentRatePageEdit(Request $request)
    {
        $student = Student::find($request->input('student_id'));
        $vacancy_id = $request->input('vacancy_id');
        $need_skills = ResumeSkillRate::where('resume_id', $student->resume->id)
            ->join('skills', 'skills.id', '=', 'resume_skill_rates.skill_id')
            ->where('employer_id', Auth::user()->id)
            ->select('resume_skill_rates.skill_rate as skill_rate', 'skills.id as skill_id', 'skills.skill_type as skill_type', 'skills.skill_name as skill_name')
            ->get();
        $ss = $student
            ->resume()
            ->join('resume_skill_rates', 'resume_skill_rates.resume_id', '=', 'resumes.id')
            ->get()
            ->pluck('skill_id')
            ->toArray();
        $skill = Skill::whereNotIn('id', $ss)->get();
        $description = Review::where('entity_id', $student->resume()->first()->id)
            ->where('reviewer_id', Auth::user()->id)
            ->where('type', 0)
            ->select('text')
            ->first();
        return view("employer.resume.edit-student-rate", compact('student', 'vacancy_id', 'need_skills', 'description', 'skill'));
    }
    public function editStudentRate(Request $request)
    {
        $student = Student::find($request->student_id);
        $updated_rates = ResumeSkillRate::where('resume_id', $student->resume->id)
            ->where('employer_id', Auth::user()->id)
            ->whereIn('skill_id', $request->skill_id)
            ->pluck('skill_id')->toArray();
        $new_rates = Skill::whereIn('id', $request->skill_id)
            ->whereNotIn('id', $updated_rates)
            ->pluck('id')->toArray();
        $rates_and_ids = [];
        array_push($rates_and_ids, $request->skill_id);
        array_push($rates_and_ids, $request->skill_rate);
        $ur = [];
        for ($i = 0; $i < count($rates_and_ids[0]); $i++) {
            if (in_array($rates_and_ids[0][$i], $updated_rates)) {
                array_push($ur, [$rates_and_ids[0][$i], $rates_and_ids[1][$i]]);
            }
        }
        $ur_skill_ids = [];
        for ($i = 0; $i < count($rates_and_ids[0]); $i++) {
            if (in_array($rates_and_ids[0][$i], $updated_rates)) {
                array_push($ur_skill_ids, $rates_and_ids[0][$i]);
            }
        }
        $nr = [];
        for ($i = 0; $i < count($rates_and_ids[0]); $i++) {
            if (in_array($rates_and_ids[0][$i], $new_rates)) {
                array_push($nr, [$rates_and_ids[0][$i], $rates_and_ids[1][$i]]);
            }
        }
        $updated_rates = ResumeSkillRate::where('resume_id', $student->resume->id)
            ->where('employer_id', Auth::user()->id)
            ->whereIn('skill_id', $ur_skill_ids)
            ->get();
        $i = 0;
        foreach ($updated_rates as $rate) {
            $rate->skill_rate = $ur[$i][1];
            $rate->save();
            $i++;
        }
        $i = 0;
        foreach ($nr as $index) {
            ResumeSkillRate::create([
                'employer_id' => Auth::User()->id,
                'resume_id' => $student->resume->id,
                'skill_id' => $nr[$i][0],
                'skill_rate' => $nr[$i][1] ?? 0,
            ]);
            $i++;
        }
        $review = Review::where('entity_id', $student->resume()->first()->id)
            ->where('reviewer_id', Auth::user()->id)->first();
        $review->text = $request->description;
        $review->save();
        return redirect(RouteServiceProvider::EMPLOYER_HOME);
    }
}
