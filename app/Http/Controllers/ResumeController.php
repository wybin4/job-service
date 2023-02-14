<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Education;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ResumeController extends Controller
{
    public function resumeDetails($id)
    {
        $resume = Resume::find($id);
        if ($resume) {
            if ($resume->about_me) {
                $about_me = nl2br(\Illuminate\Support\Str::markdown($resume->about_me));
            } else $about_me = "";
            $student_skills = StudentSkill::where('resume_id', $resume->id)
                ->join('skills', 'skills.id', '=', 'student_skills.skill_id')
                ->get();
            $work_experience = $resume->work_experience;
            return view('student.resume.resume-details', compact('resume', 'about_me', 'student_skills', 'work_experience'));
        } else return redirect()->back();
    }

    public function unarchiveResume(Request $request)
    {
        //заархивировали текущее, если оно есть
        if (Auth::User()->resume) {
            $curr_resume = Auth::User()->resume;
            $curr_resume->archived_at = date('Y-m-d H:i:s');
            $curr_resume->status = 1;
            $curr_resume->save();
        }
        if ($request->resume_id) {
            $new_resume = Resume::find($request->resume_id);
            $new_resume->archived_at = null;
            $new_resume->status = 0;
            $new_resume->save();
        }
    }
    public function viewArchivedResumesPage()
    {
        $archived_resumes = Auth::User()->archived_resumes->sortByDesc('archived_at');
        return view('student.resume.archived-resumes-feed')
            ->with('archived_resumes', $archived_resumes);
    }
    public function archiveResume(Request $request)
    {
        $student = Student::find(Auth::guard('student')->id());
        $student->newsletter_subscription = 0;
        $student->save();
        $resume = Auth::User()->resume;
        $resume->archived_at = date('Y-m-d H:i:s');
        $resume->status = 1;
        $resume->save();
        return redirect()->back();
    }
    public function addSkill(Request $request)
    {
        Skill::create([
            'skill_name' => $request->skill_name,
            'skill_type' => $request->skill_type,
        ]);
    }
    public function addProfession(Request $request)
    {
        Profession::create([
            'profession_name' => $request->profession_name,
            'subsphere_id' => $request->subsphere_id,
        ]);
    }
    public function createResume(Request $request)
    {
        $newsletter = false;
        if ($request->newsletter_subscription) {
            $newsletter = true;
        }
        $student = Student::find(Auth::guard('student')->id());
        $student->newsletter_subscription = $newsletter;
        $student->save();
        $profession_id = $request->profession_id;
        $validator = Validator::make($request->all(), [
            'profession_id' => 'required|integer',
            'type_of_employment' => 'required|integer',
            'work_type' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        Resume::create([
            'student_id' => Auth::guard('student')->id(),
            'profession_id' => $profession_id,
            'type_of_employment_id' => $request->type_of_employment,
            'work_type_id' => $request->work_type,
            'about_me' => $request->about_me,
        ]);
        if ($request->hard_skills || $request->soft_skills) {
            $i = 0;
            if ($request->soft_skills) {
                foreach ($request->soft_skills as $index) {
                    StudentSkill::create([
                        'resume_id' => Auth::User()->resume->id,
                        'skill_id' => $request->soft_skills[$i]
                    ]);
                    $i++;
                }
            }
            $i = 0;
            if ($request->hard_skills) {
                foreach ($request->hard_skills as $index) {
                    StudentSkill::create([
                        'resume_id' => Auth::User()->resume->id,
                        'skill_id' => $request->hard_skills[$i],
                        'skill_rate' => $request->skill_rate[$i] ?? 0
                    ]);
                    $i++;
                }
            }
        }
        if ($request->company_name || $request->work_title) {
            $i = 0;
            foreach ($request->company_name as $index) {
                if ($request->company_name || $request->work_title) {
                    WorkExperience::create([
                        'resume_id' => Auth::User()->resume->id,
                        'company_name' => $request->company_name[$i],
                        'location' => $request->company_location[$i],
                        'work_title' => $request->work_title[$i],
                        'date_start' => $request->work_date_start[$i],
                        'date_end' => $request->work_date_end[$i],
                        'description' => $request->work_description[$i],
                    ]);
                }
                $i++;
            }
        }
        if ($request->university_name || $request->speciality_name) {
            $i = 0;
            foreach ($request->university_name as $index) {
                if ($request->university_name || $request->speciality_name) {
                    Education::create([
                        'resume_id' => Auth::User()->resume->id,
                        'university_name' => $request->university_name[$i],
                        'location' => $request->edu_location[$i],
                        'speciality_name' => $request->speciality_name[$i],
                        'date_start' => $request->edu_date_start[$i],
                        'date_end' => $request->edu_date_end[$i],
                        'description' => $request->edu_description[$i],
                    ]);
                }
                $i++;
            }
        }
        if ($request->platform_name && $request->course_name) {
            $i = 0;
            foreach ($request->platform_name as $index) {
                if ($request->platform_name && $request->course_name) {
                    Course::create([
                        'resume_id' => Auth::User()->resume->id,
                        'platform_name' => $request->platform_name[$i],
                        'course_name' => $request->course_name[$i],
                    ]);
                }
                $i++;
            }
        }
        return redirect(RouteServiceProvider::STUDENT_HOME);
    }
    public function alterResume(Request $request)
    {
        $resume = Auth::User()->resume;
        $validator = Validator::make($request->all(), [
            'profession_id' => 'required',
            'type_of_employment' => 'required',
            'work_type' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $resume->fill([
            'profession_id' => $request->profession_id,
            'type_of_employment_id' => $request->type_of_employment,
            'work_type_id' => $request->work_type,
            'about_me' => $request->about_me,
        ]);
        $resume->save();
        return redirect(RouteServiceProvider::STUDENT_HOME);
    }
}
