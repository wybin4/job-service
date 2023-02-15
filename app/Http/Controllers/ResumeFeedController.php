<?php

namespace App\Http\Controllers;

use App\Mail\sendOfferLink;
use App\Models\Interaction;
use Illuminate\Http\Request;
use App\Models\Resume;
use App\Models\Profession;
use App\Models\ResumeSkillRate;
use App\Models\Review;
use App\Models\SphereOfActivity;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\TypeOfEmployment;
use App\Models\Vacancy;
use App\Models\WorkType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\WorkExperience;
use DateTime;
use Illuminate\Support\Facades\Auth;

class ResumeFeedController extends Controller
{

    function get_average($arr)
    {
        $summ = array_sum(array_map(function ($el) {
            return $el[1];
        }, $arr));
        $length = count($arr);
        return $summ / $length;
    }
    function get_diff($emp_val, $self_val)
    {
        return 1 - tanh((max($emp_val, $self_val) - min($emp_val, $self_val)) / 5);
    }
    public function get_ema($arr)
    {
        $alpha = 2 / (count($arr) + 1);
        $ema = $arr[0];
        for ($i = 1; $i < count($arr); $i++) {
            $ema *= (1 - $alpha);
            $ema += $arr[$i] * $alpha;
        }
        return $ema;
    }
    public function solver($a, $b, $c, $d, $e, $f)
    {
        $y = ($a * $f - $c * $d) / ($a * $e - $b * $d);
        $x = ($c * $e - $b * $f) / ($a * $e - $b * $d);
        return array($x, $y);
    }
    function get_trend($arr)
    {
        $t = $arr[0];
        $y = $arr[1];
        $square_t =
            array_map(function ($el) {
                return $el * $el;
            }, $t);
        $square_y =
            array_map(function ($el) {
                return $el * $el;
            }, $y);
        $t_x_y = array();
        for ($i = 0; $i < count($t); $i++) {
            array_push($t_x_y, $t[$i] * $y[$i]);
        }
        $sum_t = array_sum($t);
        $sum_y = array_sum($y);
        $sum_square_t = array_sum($square_t);
        $sum_square_y = array_sum($square_y);
        $sum_t_x_y = array_sum($t_x_y);
        $a_and_b = $this->solver(count($t), $sum_t, $sum_y, $sum_t, $sum_square_t, $sum_t_x_y);
        $count = ($t[count($t) - 1] - $t[0]) / 30;
        $result_t = array();
        $result_y = array();
        $val = null;
        for ($i = 0; $i < $count; $i++) {
            $val = $t[0] + 30 * $i;
            array_push($result_t, $val);
            array_push($result_y, $a_and_b[1] * $val + $a_and_b[0]);
        }
        return array($result_t, $result_y);
    }
    function _group_by($array, $key)
    {
        $resultArr = [];
        foreach ($array as $val) {
            $resultArr[$val[$key]][] = $val;
        }
        return $resultArr;
    }
    public function index(Request $request)
    {
        $work_types = WorkType::all();
        $work_exp = ['0', '<1', '1-2', '2-3', '>3'];
        $type_of_employments = TypeOfEmployment::all();
        $professions = Profession::all();
        $spheres = SphereOfActivity::all();
        if ($request->ajax()) {
            $spheres_id = [];
            $work_types_id = [];
            $type_of_employments_id = [];
            $locations_id = [];
            $work_exps_id = [];
            if ($request->exists('location') || $request->exists('sphere') || $request->exists('work_type') || $request->exists('type_of_employment') || $request->exists('work_exp')) {
                if ($request->exists('location')) {
                    $locations_id = explode(",", $request->query()['location']);
                }
                if ($request->exists('sphere')) {
                    $spheres_id = explode(",", $request->query()['sphere']);
                    $spheres_id = array_map('intval', $spheres_id);
                }
                if ($request->exists('work_type')) {
                    $work_types_id = explode(",", $request->query()['work_type']);
                    $work_types_id = array_map('intval', $work_types_id);
                }
                if ($request->exists('type_of_employment')) {
                    $type_of_employments_id = explode(",", $request->query()['type_of_employment']);
                    $type_of_employments_id = array_map('intval', $type_of_employments_id);
                }
                if ($request->exists('work_exp')) {
                    $work_exps_id = explode(",", $request->query()['work_exp']);
                }
            }

            $resumes = DB::table('resumes')
                ->join('professions', 'professions.id', '=', 'resumes.profession_id')
                ->join('subsphere_of_activities', 'subsphere_of_activities.id', '=', 'professions.subsphere_id')
                ->join('type_of_employments', 'type_of_employments.id', '=', 'resumes.type_of_employment_id')
                ->join('work_types', 'work_types.id', '=', 'resumes.work_type_id')
                ->join('students', 'students.id', '=', 'resumes.student_id')
                ->select('*', 'resumes.id as resume_id', 'resumes.created_at as resume_created_at');
            $resumes = $resumes->where('status', '=', 0);
            $loc_ids = [];
            for ($i = 0; $i < count($locations_id); $i++) {
                if ($i % 2 == 0) {
                    array_push($loc_ids, $locations_id[$i] . "," . $locations_id[$i + 1]);
                }
            }
            if ($loc_ids) {
                $resumes = $resumes->whereIn('students.location', $loc_ids);
            }
            if ($spheres_id) {
                $resumes = $resumes->whereIn('sphere_id', $spheres_id);
            }
            if ($work_types_id) {
                $resumes = $resumes->whereIn('work_type_id', $work_types_id);
            }
            if ($type_of_employments_id) {
                $resumes = $resumes->whereIn('type_of_employment_id', $type_of_employments_id);
            }
            if ($request->profession_name) {
                $resumes = $resumes->where('profession_name', '=', $request->profession_name);
            }
            $work_experiences = WorkExperience::whereIn('resume_id', $resumes->pluck('resume_id')->toArray())->get();
            $work_exps = [];
            if (count($work_experiences)) {
                $resume_id = $work_experiences[0]->resume_id;
                $result = new DateTime();
                $diff_res = clone $result;
                for ($i = 0; $i < count($work_experiences); $i++) {
                    $ds = date_create($work_experiences[$i]->date_start);
                    $de = date_create($work_experiences[$i]->date_end);
                    $diff = date_diff($ds, $de);
                    $result->add($diff);
                    if (
                        $i + 1 != count($work_experiences)
                    ) {
                        if ($work_experiences[$i + 1]->resume_id != $resume_id) {
                            if ($result->diff($diff_res)) {
                                array_push($work_exps, [$resume_id, $result->diff($diff_res)->y, $result->diff($diff_res)->m]);
                            }
                            $resume_id = $work_experiences[$i + 1]->resume_id;
                            $result = new DateTime();
                            $diff_res = clone $result;
                        }
                    } else {
                        if ($result->diff($diff_res)) {
                            array_push($work_exps, [$resume_id, $result->diff($diff_res)->y, $result->diff($diff_res)->m]);
                        }
                    }
                }
            }
            if ($work_exps_id) {
                if (strlen($work_exps_id[0]) == 1) {
                    // без опыта работы
                    $work_exp_resume_id = array_map(function ($val) {
                        return $val[0];
                    }, $work_exps);
                    $resumes = $resumes->whereNotIn('resumes.id', $work_exp_resume_id);
                } else if (strlen($work_exps_id[0]) == 2) {
                    if ($work_exps_id[0] == "<1") {
                        // опыт работы до года
                        $work_exps = array_filter($work_exps, function ($val) {
                            return $val[1] == 0 && $val[2] != 0;
                        });
                        $work_exp_resume_id = array_map(function ($val) {
                            return $val[0];
                        }, $work_exps);
                        $work_exps = array_values($work_exps);
                        $resumes = $resumes->whereIn('resumes.id', $work_exp_resume_id);
                    } else {
                        // опыт работы более трех лет
                        $work_exps = array_filter($work_exps, function ($val) {
                            return $val[1] > 3;
                        });
                        $work_exp_resume_id = array_map(function ($val) {
                            return $val[0];
                        }, $work_exps);
                        $work_exps = array_values($work_exps);

                        $resumes = $resumes->whereIn('resumes.id', $work_exp_resume_id);
                    }
                } else if (strlen($work_exps_id[0]) == 3) {
                    if ($work_exps_id[0] == "2-3") {
                        // опыт работы между 2-3 годами
                        $work_exps = array_filter($work_exps, function ($val) {
                            return $val[1] <= 3 && $val[1] >= 2;
                        });
                        $work_exp_resume_id = array_map(function ($val) {
                            return $val[0];
                        }, $work_exps);
                        $work_exps = array_values($work_exps);

                        $resumes = $resumes->whereIn('resumes.id', $work_exp_resume_id);
                    } else {
                        // опыт работы между 1-2 годами
                        $work_exps = array_filter($work_exps, function ($val) {
                            return $val[1] < 2 && $val[1] >= 1;
                        });
                        $work_exp_resume_id = array_map(function ($val) {
                            return $val[0];
                        }, $work_exps);
                        $work_exps = array_values($work_exps);

                        $resumes = $resumes->whereIn('resumes.id', $work_exp_resume_id);
                    }
                }
            }
            $employer_rates = ResumeSkillRate::whereIn(
                'student_skill_id',
                StudentSkill::whereIn('resume_id', $resumes->pluck('resume_id')->toArray())
                    ->pluck('id')
                    ->toArray()
            )
                ->join('student_skills', 'student_skills.id', '=', 'resume_skill_rates.student_skill_id')
                ->select('skill_id', 'resume_skill_rates.skill_rate', 'resume_skill_rates.updated_at', 'student_skills.resume_id')
                ->get();
            $self_rates = StudentSkill::whereIn('resume_id', $resumes->pluck('resume_id')->toArray())
                ->join('skills', 'student_skills.skill_id', '=', 'skills.id')
                ->where('skill_type', 1)
                ->select('skill_id', 'skill_rate', 'resume_id')
                ->get()->toArray();
            $rating = array();
            //сгруппированные по resume_id оценки работодателей
            $grouped_employer_rates = $this->_group_by($employer_rates, 'resume_id');
            foreach ($grouped_employer_rates as $ger) {
                $unique_skill_ids = array_unique(array_map(function ($el) {
                    return $el->skill_id;
                }, $ger)); //выделяем уникальные skill_id из оценок по одному резюме
                $employer_rates = array();

                //проходим по оценкам работодателей за каждый навык
                for ($i = 0; $i < count($unique_skill_ids); $i++) {
                    $usi = $unique_skill_ids[$i];
				    //выбираем оценки одного и того же навыка разными работодателями в одном и том же резюме
                    $current_skills = array_filter($ger, function ($el) use ($usi) {
                        return $el->skill_id == $usi;
                    });
                    //выделяем updated_at и переводим его в дни
                    $time = array_values(array_map(function ($el) {
                        return strtotime($el->updated_at->toDateString()) / (3600 * 24);
                    }, $current_skills));
                    //выделяем оценки за навык
                    $skill_rate = array_values(array_map(function ($el) {
                        return $el->skill_rate;
                    }, $current_skills));
                    //если оценок больше одной, то рассчет тренда по методу наименьших квадратов
                    if (count($current_skills) > 1) {
                        array_push($employer_rates, array($unique_skill_ids[$i], $this->get_trend(array($time, $skill_rate))));
                    } else { //если оценка только одна, то тренда не будет
                        array_push($employer_rates, array($unique_skill_ids[$i], array($time, $skill_rate)));
                    }
                }
                $employer_ema = array();
                //получаем ema для каждой оценки
                foreach ($employer_rates as $rate) {
                    array_push($employer_ema, array($rate[0], $this->get_ema($rate[1][1])));
                }
                $selfs = array();
                $weighted_self = null;
                for ($i = 0; $i < count($employer_ema); $i++) {
                    //вычисляем разность между ema по каждой оценке и самооценкой студента для получения весов
                    $weighted_self = $this->get_diff($employer_ema[$i][1], $self_rates[$i]['skill_rate']);
                    //получаем взвешенное значение самооценки
                    $weighted_self = array($employer_ema[$i][0], $weighted_self * $self_rates[$i]['skill_rate']);
                    array_push($selfs, $weighted_self);
                }
                $employer_average = $this->get_average($employer_ema); //среднее по оценкам работодателей
                $self_average = $this->get_average($selfs); //среднее по самооценке
                array_push($rating, array($ger[0]['resume_id'], $employer_average * 0.8 + $self_average * 0.2));
            }
            //отделяем те резюме, где есть оценки работодателей
            $used_resumes = array_map(function ($r) {
                return $r[0];
            }, $rating);
            $ur = $used_resumes;

            //выделяем резюме только с самооценкой
            $ungrouped_selfs = array_filter($self_rates, function ($rate) use ($ur) {
                return !in_array($rate['resume_id'], $ur);
            });
            //группируем по resume_id
            $grouped_self = array_values($this->_group_by($ungrouped_selfs, 'resume_id'));
            $self_rating = array();
            for ($i = 0; $i < count($grouped_self); $i++) {
                //вычисляем среднее по каждому резюме и умножаем на вес
                array_push($self_rating, array(
                    $grouped_self[$i][0]['resume_id'],
                    array_sum(array_map(function ($el) {
                        return $el['skill_rate'];
                    }, $grouped_self[$i])) / count($grouped_self[$i]) * 0.5
                ));
            }
            $rating = array_merge($rating, $self_rating); //объединяем все резюме
            //сортируем по оценкам
            usort($rating, function ($a, $b) {
                if ($a[1] == $b[1]) return 0;
                return ($a[1] < $b[1]) ? 1 : -1;
            });

            $rate_order = array_map(function ($el) {
                return $el[0];
            }, $rating);
            $resumes = $resumes
                ->whereIn('resumes.id', $rate_order)
                ->orderByRaw('FIELD (resumes.id, ' . implode(', ', $rate_order) . ') ASC')
                ->paginate(6);

            //$resumes = $resumes->orderBy('resume_created_at', 'desc')->paginate(6);
            return response()->json(
                [
                    'resumes' => $resumes,
                    'work_exps' => $work_exps,
                    'employer_rates' => $employer_rates,
                    'self_rates' => $self_rates,
                ]

            );
        }
        return view('employer.resume.feed')
            ->with("work_types", $work_types)
            ->with("type_of_employments", $type_of_employments)
            ->with('work_exp', $work_exp)
            ->with('professions', $professions)
            ->with('spheres', $spheres);
    }
    public function displayResumeDetails($id)
    {
        $employer_rates = StudentSkill::where('resume_id', $id)
            ->join('resume_skill_rates', 'student_skills.id', '=', 'resume_skill_rates.student_skill_id')
            ->where('student_skills.skill_rate', '>', 0)
            ->select('skill_id', 'resume_skill_rates.skill_rate', 'resume_skill_rates.updated_at')
            ->orderBy('resume_skill_rates.updated_at', 'asc')
            ->get();
        $reviews = Review::where('entity_id', $id)
            ->join('employers', 'employers.id', '=', 'reviews.reviewer_id')
            ->select('*', 'reviews.updated_at as review_updated_at')
            ->get();
        $employer_vacancies = Auth::guard('employer')->user()->active_vacancy;
        $employer_vacancies_ids = DB::table('vacancies')->select('id')->where('status', 0)->where('employer_id', Auth::user()->id)->get()->toArray();
        $ids = array();
        for ($i = 0; $i < count($employer_vacancies_ids); $i++) {
            array_push($ids, $employer_vacancies_ids[$i]->id);
        }
        $resume = Resume::find($id);
        $student = Student::find($resume->student_id);

        $binded_vacancies = DB::table('vacancies')
            ->join('interactions', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->whereIn('interactions.vacancy_id', $ids) // id вакансий именно этого работодателя
            ->where('interactions.student_id', $student->id) // есть взаимодействия с этим студентом
            ->select('vacancies.id', 'interactions.type', 'professions.profession_name')
            ->get();
        $interactions = DB::table('interactions')
            ->join('vacancies', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->where('employer_id', Auth::user()->id)
            ->where('student_id', $student->id)
            ->pluck('vacancy_id')->toArray();
        $enabeled_vacancies = DB::table('vacancies')
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->where('status', 0)
            ->where('employer_id', Auth::user()->id)
            ->whereNotIn('vacancies.id', $interactions)
            ->select('vacancies.id', 'professions.profession_name')->get();
        if ($resume->about_me) {
            $about_me = \Illuminate\Support\Str::markdown($resume->about_me);
        } else $about_me = "";
        $profession = Profession::find($resume->profession_id);
        $type_of_employment = TypeOfEmployment::find($resume->type_of_employment_id);
        $work_type = WorkType::find($resume->work_type_id);
        $student_skills = DB::table('student_skills')->join('skills', 'skills.id', '=', 'student_skills.skill_id')->where('resume_id', '=', $id)->get();
        return view('employer.resume.resume-details', compact('resume', 'profession', 'student', 'type_of_employment', 'work_type', 'student_skills', 'about_me', 'employer_vacancies', 'binded_vacancies', 'enabeled_vacancies', 'employer_rates', 'reviews'));
    }
}
