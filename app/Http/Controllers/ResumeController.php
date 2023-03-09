<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Education;
use App\Models\EmployerRate;
use App\Models\Interaction;
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
use App\Models\Vacancy;
use App\Models\VacancySkill;
use App\Models\WorkExperience;
use App\Models\WorkType;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    public function findVacancies()
    {
        $algo = new AlgorithmController;

        $resume_id = Auth::user()->resume->id;
        $binded_vacancies = Interaction::where('student_id', Auth::user()->id)
            ->pluck('vacancy_id')
            ->toArray();
        $vacancies = Vacancy::whereNotIn('vacancies.id', $binded_vacancies)
            ->where('status', 0);
        $resume = Auth::user()->resume;
        $location = Auth::user()->location;
        // очищаем от вакансий с неверным местоположением, видом раюоты и типом занятости
        if ($resume->type_of_employment_id == 3) {
            $vacancies = $vacancies->where('type_of_employment_id', '=', $resume->type_of_employment_id);
        } else {
            $vacancies = $vacancies->where('type_of_employment_id', '=', $resume->type_of_employment_id);
            $vacancies = $vacancies->where('location', '=', $location);
        }
        $vacancies = $vacancies->where('work_type_id', '=', $resume->work_type_id);
        $vacancies = $vacancies->pluck('vacancies.id')->toArray(); // очистили
        $ungrouped_vs = VacancySkill::whereIn('vacancy_id', $vacancies)->get(); // получаем навыки вакансий
        $grouped_vacancy_skills = $algo->_group_by($ungrouped_vs, 'vacancy_id');
        $curr_resume_skills = StudentSkill::where('resume_id', $resume_id)->pluck('skill_id')->toArray();
        $percent = []; // процент совпадения навыков в резюме и навыков в каждой из вакансий
        foreach ($grouped_vacancy_skills as $gvs) {
            array_push($percent, [$gvs[0]->vacancy_id, count(array_intersect($curr_resume_skills, array_map(function ($g) {
                return $g->skill_id;
            }, $gvs))) / count($curr_resume_skills)]);
        }
        usort($percent, function ($a, $b) {
            if ($a[0] == $b[0]) return 0;
            return ($a[0] < $b[0]) ? -1 : 1;
        });
        $x1 = $percent; // x1 - процент совпадения навыков
        $curr_work_exp = Auth::user()->resume->work_experience;
        if (count($curr_work_exp)) {
            $result = new DateTime();
            $diff_res = clone $result;
            for ($i = 0; $i < count($curr_work_exp); $i++) {
                $ds = date_create($curr_work_exp[$i]->date_start);
                $de = date_create($curr_work_exp[$i]->date_end);
                $diff = date_diff($ds, $de);
                $result->add($diff);
            }
            $work_exps = [$result->diff($diff_res)->y, $result->diff($diff_res)->m];
        } else $work_exps = [0, 0];

        $vacancy_work_exp = Vacancy::whereIn('vacancies.id', $vacancies)
            ->select('id', 'work_experience')
            ->get()->toArray();
        $x2 = array_map(function ($vwe) use ($work_exps) {
            $days_resume = $work_exps[1] * 30.417 + $work_exps[0] * 365;
            $days_vacancy = $vwe['work_experience'] * 365;
            return [$vwe["id"], abs($days_vacancy - $days_resume) / 365];
        }, $vacancy_work_exp); // x2 - разница в опыте работы
        $x_matrix_4 = [[0.4, 0.5, 0.7, 0.87, 0.9], [3, 3, 2, 1, 0]];
        $u4 = [1, 4];
        $y_matrix = [0.2, 0.4, 0.6, 0.8, 1];
        $equation_4 = $algo->find_linear_regression($x_matrix_4, $y_matrix, $u4);
        $z_x_matrix_4 = [];
        for ($i = 0; $i < count($x1); $i++) {
            $arr = [];
            $vacancy_id = $x1[$i][0];
            array_push($arr, $x1[$i][1]);
            array_push($arr, $x2[array_search($vacancy_id, array_column($x2, 0))][1] / 4);
            array_push($z_x_matrix_4, [$vacancy_id, $arr]);
        }
        $res_4 = array_map(function ($matrix) use ($equation_4, $algo) {
            if ($algo->vectorvectormult($equation_4, $matrix[1]) * 5 > 0) {
                return [$matrix[0], intval(round($algo->vectorvectormult($equation_4, $matrix[1]) * 5, 0))];
            }
        }, $z_x_matrix_4);
        $res_4 = array_filter($res_4, function ($r) {
            if ($r) {
                return $r;
            }
        });
        $res_4 = array_values($res_4);
        $res_4 = array_filter($res_4, function ($ro) {
            return ($ro[1] > 2);
        });
        usort($res_4, function ($a, $b) {
            if ($a[1] == $b[1]) return 0;
            return ($a[1] < $b[1]) ? 1 : -1;
        });
        $vacancy_order = $res_4;
        $vacancy_ids = array_map(function ($r) {
            return $r[0];
        }, $vacancy_order);
        if ($vacancy_ids) {
            $vacancies = DB::table('vacancies')
                ->join('employers', 'employers.id', '=', 'vacancies.employer_id')
                ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
                ->select(
                    '*',
                    'vacancies.id as vacancy_id',
                    'employers.id as employer_id',
                    'employers.name as employer_name',
                    'vacancies.created_at as vacancy_created_at'
                )
                ->whereIn('vacancies.id', $vacancy_ids)
                ->orderByRaw('FIELD (vacancies.id, ' . implode(', ', $vacancy_ids) . ') ASC')
                ->get();
            $employer_ids = Vacancy::whereIn('vacancies.id', $vacancy_ids)->groupBy("employer_id")->pluck("employer_id")->toArray();
            $employer_rates = EmployerRate::whereIn('employer_id', $employer_ids)
                ->join('employer_qualities', 'employer_qualities.id', '=', 'employer_rates.quality_id')
                ->select('*', 'employer_rates.updated_at as updated_at')
                ->orderBy('employer_rates.updated_at', 'asc')
                ->get();
            return view("student.resume.find-vacancies", compact("vacancies", "resume", "vacancy_order", "employer_rates"));
        } else {
            $vacancies = [];
            $popular_professions = Vacancy::where('status', 0)
                ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
                ->select(DB::raw('count(*) as profession_name_count, profession_name'))
                ->groupBy('profession_name')
                ->orderBy('profession_name_count', 'desc')
                ->paginate(3);
            return view("student.resume.find-vacancies", compact("popular_professions", "vacancies"));
        }
    }
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
        $validator = Validator::make(
            $request->all(),
            [
                'company_name.*' => 'required',
                'work_title.*' => 'required',
            ],
            [
                'company_name.*.required' => 'Укажите название компании',
                'work_title.*.required' => 'Укажите название должности',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $validator = Validator::make(
            $request->all(),
            [
                'university_name.*' => 'required',
                'speciality_name.*' => 'required',
            ],
            [
                'university_name.*.required' => 'Укажите название учебного заведения',
                'speciality_name.*.required' => 'Укажите название специальности',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $validator = Validator::make(
            $request->all(),
            [
                'platform_name.*' => 'required',
                'course_name.*' => 'required',
            ],
            [
                'platform_name.*.required' => 'Укажите название платформы',
                'course_name.*.required' => 'Укажите название курса',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
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
        } else {
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
    public function createResumeView()
    {
        $sphere = SphereOfActivity::all();
        $category = SubsphereOfActivity::all();
        $profession = Profession::all();
        $type_of_employment = TypeOfEmployment::all();
        $work_type = WorkType::all();
        $skill = Skill::all();
        $university = Auth::user()->university;
        return view('student.resume.create-resume')
            ->with('sphere', $sphere)
            ->with('category', $category)
            ->with('profession', $profession)
            ->with('type_of_employment', $type_of_employment)
            ->with('work_type', $work_type)
            ->with('skill', $skill)
            ->with('university', $university);
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
        $validator = Validator::make(
            $request->all(),
            [
                'company_name.*' => 'required',
                'work_title.*' => 'required',
            ],
            [
                'company_name.*.required' => 'Укажите название компании',
                'work_title.*.required' => 'Укажите название должности',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $validator = Validator::make(
            $request->all(),
            [
                'university_name.*' => 'required',
                'speciality_name.*' => 'required',
            ],
            [
                'university_name.*.required' => 'Укажите название учебного заведения',
                'speciality_name.*.required' => 'Укажите название специальности',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $validator = Validator::make(
            $request->all(),
            [
                'platform_name.*' => 'required',
                'course_name.*' => 'required',
            ],
            [
                'platform_name.*.required' => 'Укажите название платформы',
                'course_name.*.required' => 'Укажите название курса',
            ]
        );
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
