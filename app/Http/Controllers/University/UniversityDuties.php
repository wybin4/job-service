<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\AlgorithmController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpController;
use App\Models\Interaction;
use App\Models\Resume;
use App\Models\ResumeSkillRate;
use App\Models\Student;
use App\Models\StudentSkill;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniversityDuties extends Controller
{
    function get_average_marks($algo, $grouped_employer_rates)
    {
        $rating = [];
        foreach ($grouped_employer_rates as $ger) {
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
                    array_push($employer_rates, array($unique_skill_ids[$i], $algo->get_trend(array($time, $skill_rate))));
                } else { //если оценка только одна, то тренда не будет
                    array_push($employer_rates, array($unique_skill_ids[$i], array($time, $skill_rate)));
                }
            }
            $employer_ema = array();
            //получаем ema для каждой оценки
            foreach ($employer_rates as $rate) {
                array_push($employer_ema, array($rate[0], $algo->get_ema($rate[1][1])));
            }
            $employer_average = $algo->get_average($employer_ema); //среднее по оценкам работодателей
            array_push($rating, array($ger[0]['resume_id'], $employer_average));
        }
        return $rating;
    }
    public function avg_by_student($arr)
    {
        return array_sum(array_map(function ($el) {
            return $el["total"];
        }, $arr)) / count($arr);
    }
    public function viewStatictics(Request $request)
    {
        $stats = $request->stats;
        $today = Carbon::now();
        $month_ago = date('d.m.y', strtotime('-1 month'));
        $year_ago = date('d.m.y', strtotime('-1 year'));
        if ($stats == "month") {
            $start = $month_ago;
            $last_per_start = date('d.m.y', strtotime('-2 month'));
        } else {
            $start = $year_ago;
            $last_per_start = date('d.m.y', strtotime('-2 year'));
        }
        $end = $today;
        $algo = new AlgorithmController;
        $university_id = Auth::guard('university')->user()->id;
        $grouped_total_count = Student::join('resumes', 'students.id', '=', 'resumes.student_id')
            ->where('status', 0)->groupBy('university_id')
            ->select('university_id', DB::raw('count(*) as total'))->get()->toArray();
        ////
        ////
        ////
        $current_interactions = Interaction::whereBetween('interactions.hired_at', [date_format(date_create_from_format('d.m.y', $start), 'Y-m-d') . ' 00:00:00', date_format($end, 'Y-m-d') . ' 23:59:59'])
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->where('university_id', $university_id)
            ->count();
        $last_interactions = Interaction::whereBetween('interactions.hired_at', [date_format(date_create_from_format('d.m.y', $last_per_start), 'Y-m-d') . ' 00:00:00', date_format(date_create_from_format('d.m.y', $start), 'Y-m-d') . ' 23:59:59'])
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->where('university_id', $university_id)
            ->count();
        if ($last_interactions > 0) {
            $percent_interactions = ($current_interactions - $last_interactions) / $last_interactions * 100;
        } else $percent_interactions = $current_interactions * 100;
        $last_resumes = Student::where('university_id', $university_id)
            ->join('resumes', 'students.id', '=', 'resumes.student_id')
            ->whereBetween('resumes.created_at', [
                date_format(date_create_from_format('d.m.y', $last_per_start), 'Y-m-d') . ' 00:00:00',
                date_format(date_create_from_format('d.m.y', $start), 'Y-m-d') . ' 23:59:59'
            ])
            ->where('status', 0)->count();
        $last_rates = ResumeSkillRate::join('resumes', 'resume_skill_rates.resume_id', '=', 'resumes.id')
            ->join('students', 'students.id', '=', 'resumes.student_id')
            ->whereDate('resume_skill_rates.updated_at', '<=', date_format(date_create_from_format('d.m.y', $start), 'Y-m-d') . ' 00:00:00')
            ->where('university_id', $university_id)
            ->where('resumes.status', 0)
            ->select('*', 'resume_skill_rates.updated_at as updated_at')
            ->get();
        $last_grouped_rates = $algo->_group_by($last_rates, 'resume_id');
        $last_rating = $this->get_average_marks($algo, $last_grouped_rates);
        $last_x3 = array_sum(array_map(function ($el) {
            return $el[1];
        }, $last_rating)) / count($last_rating);

        /////
        ///
        ////
        //id резюме данного университета
        $uni_resumes = Student::where('university_id', $university_id)
            ->join('resumes', 'students.id', '=', 'resumes.student_id')
            ->where('status', 0)->pluck('resumes.id')->toArray();
        //количество резюме в каждом университете
        $grouped_uni_resume_count = Student::join('resumes', 'students.id', '=', 'resumes.student_id')
            ->where('status', 0)
            ->groupBy('university_id')
            ->select('university_id', DB::raw('count(*) as total'))
            ->get()->toArray();
        //общее количество резюме
        $all_resumes_count = array_sum(array_map(function ($r) {
            return $r["total"];
        }, $grouped_uni_resume_count));
        $all_uni_resumes_count = array_map(function ($all) use ($all_resumes_count) {
            return $all["total"] / $all_resumes_count;
        }, $grouped_uni_resume_count);
        $x1 = count($uni_resumes) / $all_resumes_count;
        $x1_result = $algo->z_normalize($x1, $all_uni_resumes_count);
        ///
        ///
        ///
        //все офферы студентам за период
        $ungrouped_uni_offers_count = Interaction::whereBetween('interactions.created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->where('type', 1)
            ->groupBy('students.id')
            ->select('university_id', 'students.id', DB::raw('count(*) as total'))
            ->get()
            ->toArray();
        //группируем офферы по вузам
        $grouped_uni_offers_count = $algo->_group_by($ungrouped_uni_offers_count, "university_id");
        //офферы текущего вуза
        $current_uni_offers_count = array_values(array_filter($grouped_uni_offers_count, function ($all) use ($university_id) {
            return $all[0]['university_id'] == $university_id;
        }));
        if ($current_uni_offers_count) {
            $current_uni_offers_count = $current_uni_offers_count[0];
            //среднее количество офферов студенту по данному вузу
            $x2 = $this->avg_by_student($current_uni_offers_count);
            //среднее количество офферов студенту по каждому
            $all_x2 = array_map(function ($uni) {
                return $this->avg_by_student($uni);
            }, $grouped_uni_offers_count);
            $x2_result = $algo->z_normalize($x2, $all_x2);
        } else {
            $x2_result = -1;
        }

        ///
        //
        //
        //количество работающих студентов всех вузов
        $with_work = Interaction::whereIn('status', [3, 8])
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->groupBy('university_id')
            ->select('university_id', DB::raw('count(*) as total'))
            ->get()
            ->toArray();
        //количество работающих студентов данного вуза
        $current_uni_with_work = array_values(array_filter($with_work, function ($all) use ($university_id) {
            return $all['university_id'] == $university_id;
        }));
        if ($current_uni_with_work) {
            $current_uni_with_work = $current_uni_with_work[0]["total"];
            $x5 = $current_uni_with_work / count($uni_resumes);
            $all_x5 = array_map(function ($uni) use ($grouped_total_count) {
                return $uni["total"] / $grouped_total_count[array_search($uni["university_id"], $grouped_total_count)]["total"];
            }, $with_work);
            $x5_result = $algo->z_normalize($x5, $all_x5);
        } else {
            $x5_result = -1;
        }

        //
        //
        ///
        ///
        ///
        //оценки всех скиллов всех резюме
        $all_rates = ResumeSkillRate::join('resumes', 'resume_skill_rates.resume_id', '=', 'resumes.id')
            ->where('status', 0)
            ->select('*', 'resume_skill_rates.updated_at as updated_at')
            ->get();
        //группируем скиллы по resume_id
        $all_grouped_rates = $algo->_group_by($all_rates, 'resume_id');
        //получаем оценки по всем резюме
        $all_rating = $this->get_average_marks($algo, $all_grouped_rates);
        //получаем id resume
        $all_rate_ids = array_map(function ($r) {
            return $r[0];
        }, $all_rating);
        //получаем вузы по оценённым резюме
        $ungrouped_uni_without_rates = Resume::whereIn('resumes.id', $all_rate_ids)
            ->join('students', 'students.id', '=', 'resumes.student_id')
            ->select('resumes.id as resume_id', 'university_id')
            ->get()
            ->toArray();
        $ungrouped_uni_rates = [];
        //группируем резюме с оценками по вузам
        foreach ($ungrouped_uni_without_rates as $all) {
            array_push($ungrouped_uni_rates, array('university_id' => $all["university_id"], 'resume_id' => $all["resume_id"], 'total' => $all_rating[array_search($all["resume_id"], $all_rate_ids)][1]));
        };
        $grouped_uni_rates = $algo->_group_by($ungrouped_uni_rates, "university_id");
        $all_x3 = array_map(function ($uni) {
            return [$uni[0]["university_id"], $this->avg_by_student($uni)];
        }, $grouped_uni_rates);
        //оценки резюме текущего вуза
        $current_uni_rates = array_values(array_filter($all_x3, function ($all) use ($university_id) {
            return $all[0] == $university_id;
        }));
        if ($current_uni_rates) {
            $current_uni_rates = $current_uni_rates[0][1]; //средняя оценка по всем резюме
            $all_rate = array_sum(array_map(function ($el) {
                return $el[1];
            }, $all_rating)) / count($all_rating);
            $x3 = $current_uni_rates / $all_rate; //относительная оценка по текущему вузу
            $all_x3 = array_map(function ($uni) use ($all_rate) {
                return $uni[1] / $all_rate;
            }, $all_x3); //относительные оценки по всем вузам
            $x3_result = $algo->z_normalize($x3, $all_x3);
        } else {
            $x3_result = -1;
        }
        //
        //
        //
        //
        //
        //опыт работы по всем резюме всех вузов
        $work_experience = Interaction::whereIn('interactions.status', [3, 8, 9])
            ->join('students', 'students.id', '=', 'interactions.student_id')
            ->join('resumes', 'students.id', '=', 'resumes.student_id')
            ->select('resumes.id as resume_id', 'resumes.work_type_id', 'interactions.hired_at', 'interactions.updated_at', 'university_id', 'interactions.status')
            ->get()
            ->toArray();
        $work_experience_days = [];
        //считаем разность
        foreach ($work_experience as $we) {
            //если работает, то до сегодняшнего дня
            if ($we["status"] == 8 || $we["status"] == 3) {
                $ds = date_create($we["hired_at"]);
                $de = $today;
                //если уже там не работает, то дата выставлена 9 статуса - дата найма
            } else if ($we["status"] == 9) {
                $ds = date_create($we["hired_at"]);
                $de = date_create($we["updated_at"]);
            }
            $diff = date_diff($ds, $de);
            //переводим в дни
            $days = 365.25 * $diff->y + 30.4167 * $diff->m + $diff->d;
            //если что-то пошло не так и опыт работы больше 12 лет
            if ($days > 4380) {
                $days = 4380;
            }
            array_push($work_experience_days, ["resume_id" => $we["resume_id"], "university_id" => $we["university_id"], "work_type_id" => $we["work_type_id"],  "days" => $days]);
        }
        //группируем по вузу
        $work_experience = $algo->_group_by($work_experience_days, 'university_id');
        $grouped_uni_we = [];
        foreach ($work_experience as $we) {
            //внутри вуза группируем по типу работу
            $grouped_by_wt = $algo->_group_by($we, 'work_type_id');
            $avg_wt = [];
            //по каждому типу работы получаем среднее количество дней
            foreach ($grouped_by_wt as $wt) {
                array_push($avg_wt, [$wt[0]["work_type_id"], array_sum(array_map(function ($el) {
                    return $el["days"];
                }, $wt)) / count($wt)]);
            }
            array_push($grouped_uni_we, ["university_id" => $we[0]["university_id"], "total" => $avg_wt]);
        }
        //получаем опыты работы у текущего вуза
        $x4 = array_values(array_filter($grouped_uni_we, function ($all) use ($university_id) {
            return $all['university_id'] == $university_id;
        }));
        if ($x4) {
            $x4 = $x4[0]["total"]; //опыт работы по всем вузам
            $all_x4 = array_map(function ($all) {
                return $all["total"];
            }, $grouped_uni_we);
            $x4_result = [];
            //соотносим опыт работы по каждому виду у текущего вуза с остальными
            foreach ($x4 as $i) {
                $arr = [];
                foreach ($all_x4 as $all) {
                    array_push($arr, ...array_filter($all, function ($a) use ($i) {
                        return $a[0] == $i[0];
                    }));
                }
                $arr = array_map(function ($a) {
                    return $a[1];
                }, $arr);
                array_push($x4_result, $algo->z_normalize($i[1], $arr));
            }
            $x4_result = array_sum($x4_result) / count($x4_result);
        } else {
            $x4_result = -1;
        }
        $rating = $x1_result + $x2_result + $x3_result + $x4_result + $x5_result + 100;
        //
        if ($last_resumes > 0) {
            $percent_resumes = (count($uni_resumes) - $last_resumes) / $last_resumes * 100;
        } else $percent_resumes = count($uni_resumes) * 100;
        $rate_percent = ($current_uni_rates - $last_x3) / $last_x3 * 100;
        return view(
            'university.statictics',
            compact("rating", "today", "month_ago", "year_ago", "stats", "current_interactions", "uni_resumes", "current_uni_rates", "percent_interactions", "percent_resumes", "rate_percent"),
            compact("x1_result", "x2_result", "x3_result", "x4_result", "x5_result", "current_uni_with_work")
        );
    }
}
