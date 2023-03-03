<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use App\Models\Resume;
use App\Models\ResumeSkillRate;
use App\Models\Student;
use App\Models\StudentSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniversityDuties extends Controller
{
    function get_average_marks($grouped_employer_rates, $self_rates)
    {
        $rating = [];
        $employer_averages = [];
        foreach ($grouped_employer_rates as $ger) {
            $resume_id = $ger[0]->resume_id;
            $unique_skill_ids = array_unique(array_map(function ($el) {
                return $el->skill_id;
            }, $ger)); //выделяем уникальные skill_id из оценок по одному резюме
            $employer_rates = array();
            $unique_skill_ids = array_values($unique_skill_ids);
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
            //выделяем из всех оценок студентов оценки текущего резюме
            $current_selfs = array_filter($self_rates, function ($sr) use ($resume_id) {
                return $sr["resume_id"] == $resume_id;
            });
            $current_selfs = array_values($current_selfs);
            //выделяем те ema_ids, что есть в student_skills - которые мы можем сравнить
            $need_ema_ids = array_map(function ($self) {
                return $self["skill_id"];
            }, $current_selfs);

            $need_ema = array_values(array_filter($employer_ema, function ($ema) use ($need_ema_ids) {
                return in_array($ema[0], $need_ema_ids);
            }));

            $weighted_self = null;
            for ($i = 0; $i < count($need_ema); $i++) {
                //вычисляем разность между ema по каждой оценке и самооценкой студента для получения весов
                $weighted_self = $this->get_diff($need_ema[$i][1], $current_selfs[$i]['skill_rate']);
                //получаем взвешенное значение самооценки
                $weighted_self = array($need_ema[$i][0], $weighted_self * $current_selfs[$i]['skill_rate']);
                array_push($selfs, $weighted_self);
            }
            $employer_average = $this->get_average($employer_ema); //среднее по оценкам работодателей
            $self_average = $this->get_average($selfs); //среднее по самооценке
            array_push($rating, array($ger[0]['resume_id'], $employer_average));
        }
        return $rating;
    }
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
    public function viewStatictics()
    {
        $total_count = Student::where('university_id', Auth::guard('university')->user()->id)->pluck('id')->toArray();
        $all_resumes = Resume::where('status', 0)->count();
        $uni_resumes = Student::where('university_id', Auth::guard('university')->user()->id)
            ->join('resumes', 'students.id', '=', 'resumes.student_id')
            ->where('status', 0)->pluck('resumes.id')->toArray();
        $x1 = count($uni_resumes) / $all_resumes;
        //$with_work = Interaction::whereIn('student_id', $total_count)->whereIn('status', [3, 8, 9])->distinct()->count('student_id');
        $with_work = Interaction::whereIn('student_id', $total_count)
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->where('university_id', Auth::guard('university')->user()->id)
            ->whereIn('status', [3, 8])->get();
        $x5 = count($with_work->toArray()) / count($uni_resumes);
        $uni_employer_rates = ResumeSkillRate::join('resumes', 'resume_skill_rates.resume_id', '=', 'resumes.id')
            ->whereIn('resumes.id', $uni_resumes)
            ->select('*', 'resume_skill_rates.updated_at as updated_at')
            ->get();
        $uni_grouped_employer_rates = $this->_group_by($uni_employer_rates, 'resume_id');
        $uni_self_rates = StudentSkill::whereIn('resume_id', $uni_resumes)
            ->select('skill_id', 'skill_rate', 'resume_id')
            ->get()->toArray();
        $all_rates = ResumeSkillRate::join('resumes', 'resume_skill_rates.resume_id', '=', 'resumes.id')
            ->where('status', 0)
            ->select('*', 'resume_skill_rates.updated_at as updated_at')
            ->get();
        $all_grouped_rates = $this->_group_by($all_rates, 'resume_id');
        $all_self_rates = StudentSkill::join('resumes', 'student_skills.resume_id', '=', 'resumes.id')
            ->where('status', 0)
            ->select('skill_id', 'skill_rate', 'resume_id')
            ->get()->toArray();
        $rating = $this->get_average_marks($uni_grouped_employer_rates, $uni_self_rates);
        $all_rating = $this->get_average_marks($all_grouped_rates, $all_self_rates);
        $uni_rate = array_sum(array_map(function ($el) {
            return $el[1];
        }, $rating)) / count($rating);
        $all_rate = array_sum(array_map(function ($el) {
            return $el[1];
        }, $all_rating)) / count($all_rating);
        $x3 = $uni_rate / $all_rate;
        
        return view('university.statictics');
    }
}
