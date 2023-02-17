<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Education;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\SphereOfActivity;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\SubsphereOfActivity;
use App\Models\TypeOfEmployment;
use App\Models\WorkExperience;
use App\Models\WorkType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ResumeController extends Controller
{
    public function addExperience(Request $request)
    {
        WorkExperience::create([
            'resume_id' => Auth::User()->resume->id,
            'company_name' => $request->company_name,
            'location' => $request->company_location,
            'work_title' => $request->work_title,
            'date_start' => date_create($request->date_start),
            'date_end' => date_create($request->date_end),
        ]);
        return redirect()->back()->with('title', 'Добавление опыта')->with('text', 'Опыт успешно добавлен в резюме');
    }
    public function editResumePage($id)
    {
        $resume = Resume::find($id);
        if ($resume->about_me) {
            $about_me = nl2br(\Illuminate\Support\Str::markdown($resume->about_me));
        } else $about_me = "";
        $profession = Profession::find($resume->profession_id);
        $category = SubsphereOfActivity::find($profession->subsphere_id);
        $sphere = SphereOfActivity::find($category->sphere_id);

        $type_of_employment = TypeOfEmployment::find($resume->type_of_employment_id);
        $work_type = WorkType::find($resume->work_type_id);
        $student_skills = StudentSkill::where('resume_id', $id)->get()->pluck('skill_id')->toArray();
        $skill = Skill::whereNotIn('id', $student_skills)->get();
        $work_experiences = $resume->work_experience;
        $educations = $resume->education;
        $courses = $resume->course;
        return view('student.resume.edit-resume', compact('resume', 'about_me', 'student_skills', 'work_experiences', 'educations', 'courses'))
            ->with('sphere', $sphere)
            ->with('category', $category)
            ->with('profession', $profession)
            ->with('type_of_employment', $type_of_employment)
            ->with('work_type', $work_type)
            ->with('skill', $skill);
    }
    public function editResume(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'type_of_employment' => 'required|integer',
                'work_type' => 'required|integer',
            ]
        );
        if ($request->skills) {
            $i = 0;
            if ($request->skills) {
                foreach ($request->skills as $index) {
                    StudentSkill::create([
                        'resume_id' => $request->resume_id,
                        'skill_id' => $request->skills[$i],
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
                    if ($request->work_experience_id[$i] != "-1") {
                        $wexp = WorkExperience::find($request->work_experience_id[$i]);
                        WorkExperience::where('id', $request->work_experience_id[$i])
                            ->update([
                                'company_name' => $request->company_name[$i],
                                'location' => $request->company_location[$i],
                                'work_title' => $request->work_title[$i],
                                'date_start' => $request->work_date_start[$i],
                                'date_end' => $request->work_date_end[$i],
                                'description' => $request->work_description[$i],
                            ]);
                    } else {
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
                }
                $i++;
            }
        }
        if ($request->university_name || $request->speciality_name) {
            $i = 0;
            foreach ($request->university_name as $index) {
                if ($request->university_name || $request->speciality_name) {
                    if ($request->education_id[$i] != "-1") {
                        Education::where('id', $request->education_id[$i])
                            ->update([
                                'university_name' => $request->university_name[$i],
                                'location' => $request->edu_location[$i],
                                'speciality_name' => $request->speciality_name[$i],
                                'date_start' => $request->edu_date_start[$i],
                                'date_end' => $request->edu_date_end[$i],
                                'description' => $request->edu_description[$i],
                            ]);
                    } else {
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
                }
                $i++;
            }
        }
        if ($request->platform_name && $request->course_name) {
            $i = 0;
            foreach ($request->platform_name as $index) {
                if ($request->platform_name && $request->course_name) {
                    if ($request->course_id[$i] != "-1") {
                        Course::where('id', $request->course_id[$i])
                            ->update([
                                'resume_id' => Auth::User()->resume->id,
                                'platform_name' => $request->platform_name[$i],
                                'course_name' => $request->course_name[$i],
                            ]);
                    } else {
                        Course::create([
                            'resume_id' => Auth::User()->resume->id,
                            'platform_name' => $request->platform_name[$i],
                            'course_name' => $request->course_name[$i],
                        ]);
                    }
                }
                $i++;
            }
        }
        $resume = Resume::find($request->resume_id);
        $resume->about_me = $request->about_me;
        $resume->save();
        return redirect(RouteServiceProvider::STUDENT_HOME)->with('title', 'Редактирование резюме')->with('text', 'Резюме успешно отредактировано');
    }
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
        return redirect()->back()->with('title', 'Восстановление резюме')->with('text', 'Резюме успешно восстановлено');
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
        return redirect()->back()->with('title', 'Архивация резюме')->with('text', 'Резюме успешно архивировано');
    }
    public function addSkill(Request $request)
    {
        Skill::create([
            'skill_name' => $request->skill_name,
            'skill_type' => $request->skill_type,
        ]);
        if ($request->skill_type == 0) {
            return redirect()->back()->with('title', 'Добавление качества')->with('text', 'Успешно добавили качество');
        }else {
            return redirect()->back()->with('title', 'Добавление навыка')->with('text', 'Успешно добавили навык');
        }
    }
    public function addProfession(Request $request)
    {
        Profession::create([
            'profession_name' => $request->profession_name,
            'subsphere_id' => $request->subsphere_id,
        ]);
        return redirect()->back()->with('title', 'Добавление профессии')->with('text', 'Успешно добавили профессию');
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
        if ($request->skills) {
            $i = 0;
            if ($request->skills) {
                foreach ($request->skills as $index) {
                    StudentSkill::create([
                        'resume_id' => Auth::User()->resume->id,
                        'skill_id' => $request->skills[$i],
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
        return redirect(RouteServiceProvider::STUDENT_HOME)->with('title', 'Создание резюме')->with('text', 'Резюме успешно создано');
    }
}
