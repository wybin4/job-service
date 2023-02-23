<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\EmployerRate;
use App\Models\Interaction;
use App\Models\Profession;
use App\Models\SphereOfActivity;
use App\Models\Interactions;
use App\Models\Review;
use App\Models\TypeOfEmployment;
use App\Models\Vacancy;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VacancyFeedController extends Controller
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
        $min_salary = Vacancy::min('salary');
        $max_salary = Vacancy::max('salary');
        $work_types = WorkType::all();
        $work_exp = ['0', '<1', '1-2', '2-3', '>3'];
        $type_of_employments = TypeOfEmployment::all();
        $professions = Profession::all();
        $spheres = SphereOfActivity::all();
        if ($request->ajax()) {
            $spheres_id = [];
            $work_types_id = [];
            $type_of_employments_id = [];
            $work_exps_id = [];
            $locations_id = [];
            $filter_max_salary = [];
            if ($request->exists('location') || $request->exists('sphere') || $request->exists('work_type') || $request->exists('type_of_employment') || $request->exists('salary') || $request->exists('work_exp')) {
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
                if ($request->exists('salary')) {
                    $filter_salary = explode(",", $request->query()['salary']);
                    $filter_min_salary = $filter_salary[0];
                    $filter_max_salary = $filter_salary[1];
                }
                if ($request->exists('work_exp')) {
                    $work_exps_id = explode(",", $request->query()['work_exp']);
                }
            }
            $vacancies = DB::table('vacancies')
                ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
                ->join('subsphere_of_activities', 'subsphere_of_activities.id', '=', 'professions.subsphere_id')
                ->join('type_of_employments', 'type_of_employments.id', '=', 'vacancies.type_of_employment_id')
                ->join('work_types', 'work_types.id', '=', 'vacancies.work_type_id')
                ->join('employers', 'employers.id', '=', 'vacancies.employer_id')
                ->select('*', 'vacancies.id as vacancy_id', 'employers.id as employer_id', 'employers.name as employer_name', 'vacancies.created_at as vacancy_created_at', 'vacancies.description as vacancy_description');
            $loc_ids = [];
            for ($i = 0; $i < count($locations_id); $i++) {
                if ($i % 2 == 0) {
                    array_push($loc_ids, $locations_id[$i] . "," . $locations_id[$i + 1]);
                }
            }
            if ($loc_ids) {
                $vacancies = $vacancies->whereIn('vacancies.location', $loc_ids);
            }
            if ($spheres_id) {
                $vacancies = $vacancies->whereIn('sphere_id', $spheres_id);
            }
            if ($work_types_id) {
                $vacancies = $vacancies->whereIn('work_type_id', $work_types_id);
            }
            if ($type_of_employments_id) {
                $vacancies = $vacancies->whereIn('type_of_employment_id', $type_of_employments_id);
            }
            if ($filter_max_salary) {
                $vacancies = $vacancies->whereBetween('salary', [$filter_min_salary, $filter_max_salary]);
            }
            if ($work_exps_id) {
                if (strlen($work_exps_id[0]) == 1) {
                    $vacancies = $vacancies->where('work_experience', '=', $work_exps_id[0]);
                } else if (strlen($work_exps_id[0]) == 2) {
                    if ($work_exps_id[0] == "<1") {
                        $vacancies = $vacancies->where('work_experience', '<', $work_exps_id[0][1]);
                    } else {
                        $vacancies = $vacancies->where('work_experience', '>', $work_exps_id[0][1]);
                    }
                } else if (strlen($work_exps_id[0]) == 3) {
                    $vacancies = $vacancies->whereBetween('work_experience', [$work_exps_id[0][0], $work_exps_id[0][2]]);
                }
            }
            if ($request->profession_name) {
                $vacancies = $vacancies->where('profession_name', '=', $request->profession_name);
            }

            ////

            $all_employers = Employer::pluck('id')->toArray();
            $employer_rates = EmployerRate::join('employer_qualities', 'employer_qualities.id', '=', 'employer_rates.quality_id')
                ->orderBy('employer_rates.updated_at', 'asc')
                ->select('*', 'employer_rates.updated_at as updated_at')
                ->get();
            $rating = array();
            //сгруппированные по employer_id оценки работодателей
            $grouped_employer_rates = $this->_group_by($employer_rates, 'employer_id');
            foreach ($grouped_employer_rates as $ger) {
                $employer_id = $ger[0]->employer_id;
                $unique_quality_ids = array_unique(array_map(function ($el) {
                    return $el->quality_id;
                }, $ger)); //выделяем уникальные quality_id из оценок по одному работодателю
                $employer_rates = array();
                $unique_quality_ids = array_values($unique_quality_ids);
                //проходим по оценкам за каждого работодателя
                for ($i = 0; $i < count($unique_quality_ids); $i++) {
                    $usi = $unique_quality_ids[$i];
                    //выбираем оценки одного и того же качества для данного работодателя
                    $current_qualities = array_filter($ger, function ($el) use ($usi) {
                        return $el->quality_id == $usi;
                    });
                    //выделяем updated_at и переводим его в дни
                    $time = array_values(array_map(function ($el) {
                        return strtotime($el->updated_at->toDateString()) / (3600 * 24);
                    }, $current_qualities));
                    //выделяем оценки за навык
                    $quality_rate = array_values(array_map(function ($el) {
                        return $el->quality_rate;
                    }, $current_qualities));
                    //если оценок больше одной, то рассчет тренда по методу наименьших квадратов
                    if (count($current_qualities) > 1) {
                        array_push($employer_rates, array($unique_quality_ids[$i], $this->get_trend(array($time, $quality_rate))));
                    } else { //если оценка только одна, то тренда не будет
                        array_push($employer_rates, array($unique_quality_ids[$i], array($time, $quality_rate)));
                    }
                }
                $employer_ema = array();
                //получаем ema для каждой оценки
                foreach ($employer_rates as $rate) {
                    array_push($employer_ema, array($rate[0], $this->get_ema($rate[1][1])));
                }

                $employer_average = $this->get_average($employer_ema); //среднее по оценкам работодателя
                array_push($rating, array($ger[0]['employer_id'], $employer_average));
            }
            //отделяем оцененных работодателей
            $used_employers = array_map(function ($r) {
                return $r[0];
            }, $rating);
            $ue = $used_employers;
            //выделяем резюме только с самооценкой
            $ungrouped = array_filter($all_employers, function ($all) use ($ue) {
                return !in_array($all, $ue);
            });
            $nonused = array();
            //всех работодателей без оценок оцениваем как 4.2
            foreach ($ungrouped as $u) {
                array_push($nonused, [$u, 4.2]);
            }
            $rating = array_merge($rating, $nonused); //объединяем всех работодателей
            //сортируем по оценкам
            usort($rating, function ($a, $b) {
                if ($a[1] == $b[1]) return 0;
                return ($a[1] < $b[1]) ? 1 : -1;
            });

            $rate_order = array_map(function ($el) {
                return $el[0];
            }, $rating);

            /////


            $vacancies = $vacancies->where('status', '=', 0);
            $vacancies = $vacancies->orderByRaw('FIELD (employers.id, ' . implode(', ', $rate_order) . ') ASC')->paginate(6);

            //$vacancies = $vacancies->orderBy('vacancy_created_at', 'desc')->paginate(6);
            return response()->json(
                [
                    'vacancies' => $vacancies,
                ]

            );
        }
        return view('student.vacancy.feed')
            ->with("work_types", $work_types)
            ->with("type_of_employments", $type_of_employments)
            ->with('min_salary', $min_salary)
            ->with('max_salary', $max_salary)
            ->with('work_exp', $work_exp)
            ->with('professions', $professions)
            ->with('spheres', $spheres);
    }
    function displayVacancyDetails($id)
    {
        $response_or_not = count(Interaction::where('student_id', Auth::user()->id)->where('vacancy_id', $id)->where('type', 0)->get());
        $offer_or_not = count(Interaction::where('student_id', Auth::user()->id)->where('vacancy_id', $id)->where('type', 1)->get());
        $vacancy = Vacancy::find($id);
        if ($vacancy->description) {
            $parsed_desc = \Illuminate\Support\Str::markdown($vacancy->description);
        } else $parsed_desc = "";
        $profession = Profession::find($vacancy->profession_id);
        $employer = Employer::find($vacancy->employer_id);
        $type_of_employment = TypeOfEmployment::find($vacancy->type_of_employment_id);
        $work_type = WorkType::find($vacancy->work_type_id);
        $vacancy_skills = DB::table('vacancy_skills')->join('skills', 'skills.id', '=', 'vacancy_skills.skill_id')->where('vacancy_id', '=', $id)->get();
        $subsphere = Profession::find($vacancy->profession_id);
        $subsphere = $subsphere->subsphere_id;
        $related_vacancies = DB::table('vacancies')
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->join('work_types', 'work_types.id', '=', 'vacancies.work_type_id')
            ->join('type_of_employments', 'type_of_employments.id', '=', 'vacancies.type_of_employment_id')
            ->select('*', 'vacancies.id as vacancy_id', 'vacancies.created_at as vacancy_created_at');
        $related_vacancies = $related_vacancies->where('subsphere_id', '=', $subsphere);
        $related_vacancies = $related_vacancies->whereNot('vacancies.id', '=', $id);
        $related_vacancies = $related_vacancies->where('status', '=', 0);
        if (count($related_vacancies->where('type_of_employment_id', '=', $vacancy->type_of_employment_id)->get()) != 0) {
            if ($vacancy->type_of_employment_id == 3) {
                $related_vacancies = $related_vacancies->where('type_of_employment_id', '=', $vacancy->type_of_employment_id);
            } else {
                $related_vacancies = $related_vacancies->where('type_of_employment_id', '=', $vacancy->type_of_employment_id);
                $related_vacancies = $related_vacancies->where('location', '=', $vacancy->location);
            }
        } else if (count($related_vacancies->where('work_type_id', '=', $vacancy->work_type_id)->get()) != 0) {
            $related_vacancies = $related_vacancies->where('work_type_id', '=', $vacancy->work_type_id);
        }
        $related_vacancies = $related_vacancies->orderBy('vacancy_created_at', 'desc')->paginate(3);

        return view('student.vacancy.vacancy-details', compact('vacancy', 'profession', 'employer', 'type_of_employment', 'work_type', 'vacancy_skills', 'parsed_desc', 'related_vacancies', 'response_or_not', 'offer_or_not'));
    }
    function displayEmployerDetails($id)
    {
        $employer = Employer::find($id);
        if ($employer->description) {
            $parsed_desc = \Illuminate\Support\Str::markdown($employer->description);
        } else $parsed_desc = "";
        $employer_rates = EmployerRate::where('employer_id', $id)
            ->join('employer_qualities', 'employer_qualities.id', '=', 'employer_rates.quality_id')
            ->select('*', 'employer_rates.updated_at as updated_at')
            ->orderBy('employer_rates.updated_at', 'asc')
            ->get();
        $students_count = EmployerRate::where('employer_id', $id)
            ->distinct()
            ->count('student_id');
        $reviews = Review::where('entity_id', $id)
            ->where('type', 1)
            ->join('students', 'students.id', '=', 'reviews.reviewer_id')
            ->select('*', 'reviews.updated_at as review_updated_at')
            ->get();
        $latest_vacancies = DB::table('vacancies')
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->join('work_types', 'work_types.id', '=', 'vacancies.work_type_id')
            ->join('type_of_employments', 'type_of_employments.id', '=', 'vacancies.type_of_employment_id')
            ->select('*', 'vacancies.id as vacancy_id', 'vacancies.created_at as vacancy_created_at');
        $latest_vacancies = $latest_vacancies->where('employer_id', '=', $id);
        $latest_vacancies = $latest_vacancies->where('status', '=', 0);
        $latest_vacancies = $latest_vacancies->orderBy('vacancy_created_at', 'desc')->paginate(3);
        return view('student.vacancy.employer-details', compact('employer', 'parsed_desc', 'latest_vacancies', 'employer_rates', 'reviews', 'students_count'));
    }
}
