<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AlgorithmController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelpController;
use App\Models\EmployerRate;
use App\Models\Interaction;
use App\Models\Profession;
use App\Models\Resume;
use App\Models\ResumeSkillRate;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\SphereOfActivity;
use App\Models\SubsphereOfActivity;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDuties extends Controller
{
    public function statisticsView()
    {
        $algo = new AlgorithmController;
        $help = new HelpController;
        //оценки всех скиллов всех резюме
        $all_resume_rates = ResumeSkillRate::join('resumes', 'resume_skill_rates.resume_id', '=', 'resumes.id')
            ->where('status', 0)
            ->select('*', 'resume_skill_rates.updated_at as updated_at')
            ->get();
        //группируем скиллы по resume_id
        $all_grouped_resume_rates = $algo->_group_by($all_resume_rates, 'resume_id');
        //получаем оценки по всем резюме
        $all_resume_rating = $help->get_average_marks($algo, $all_grouped_resume_rates);
        $all_resume_rating = array_map(function ($all) {
            return round($all[1], 0);
        }, $all_resume_rating);
        $csat_employer = $algo->get_csat($all_resume_rating);
        //
        /////
        ///
        //оценки всех qualities всех работодателей
        $all_employer_rates = EmployerRate::join('employers', 'employer_rates.employer_id', '=', 'employers.id')
        ->select('*', 'employer_rates.updated_at as updated_at')
        ->get();
        //группируем скиллы по employer_id
        $all_employer_grouped_rates = $algo->_group_by($all_employer_rates, 'employer_id');
        //получаем оценки по всем работодателям
        $all_employer_rating = $help->get_average_quality_marks($algo, $all_employer_grouped_rates);
        $all_employer_rating = array_map(function ($all) {
            return round($all[1], 0);
        }, $all_employer_rating);
        $csat_student = $algo->get_csat($all_employer_rating);
        /////
        ////
        ////
        ///
        /////
        $today = Carbon::now();
        $month_ago = date('d.m.y', strtotime('-1 month'));
        //прирост резюме за месяц
        $curr_resume = Resume::where('status', 0)->count();
        $last_month_resume = Resume::whereDate('created_at', '<=', date_format(date_create_from_format('d.m.y', $month_ago), 'Y-m-d') . ' 00:00:00')
            ->where('status', 0)->count();
        $percent_resume = ($curr_resume - $last_month_resume) /  $last_month_resume * 100;
        //прирост вакансий за месяц
        $curr_vacancy = Vacancy::where('status', 0)->count();
        $last_month_vacancy = Vacancy::whereDate('created_at', '<=', date_format(date_create_from_format('d.m.y', $month_ago), 'Y-m-d') . ' 00:00:00')->where('status', 0)->count();
        $percent_vacancy = ($curr_vacancy - $last_month_vacancy) /  $last_month_vacancy * 100;
        //конкуренция
        $rivalry = $curr_resume / $curr_vacancy;
        $resume = Resume::selectRaw('year(created_at) year, month(created_at) month_numb, count(*) data')
            ->groupBy('year', 'month_numb')
            ->orderBy('year', 'desc')
            ->orderBy('month_numb', 'desc')
            ->limit(6)
            ->get();
        $vacancy = Vacancy::selectRaw('year(created_at) year, month(created_at) month_numb, count(*) data')
            ->groupBy('year', 'month_numb')
            ->orderBy('year', 'desc')
            ->orderBy('month_numb', 'desc')
            ->limit(6)
            ->get();
        /////
        ///
        //////
        ////
        $offers = Interaction::where('type', 1)->selectRaw('year(created_at) year, month(created_at) month_numb, count(*) data')
        ->groupBy('year', 'month_numb')
        ->orderBy('year', 'desc')
        ->orderBy('month_numb', 'desc')
        ->limit(6)
        ->get();
        $responses = Interaction::where('type', 0)->selectRaw('year(created_at) year, month(created_at) month_numb, count(*) data')
        ->groupBy('year', 'month_numb')
        ->orderBy('year', 'desc')
        ->orderBy('month_numb', 'desc')
        ->limit(6)
        ->get();
        $employments = Interaction::selectRaw('year(hired_at) year, month(hired_at) month_numb, count(*) data')
        ->whereNotNull('hired_at')
        ->groupBy('year', 'month_numb')
        ->orderBy('year', 'desc')
        ->orderBy('month_numb', 'desc')
        ->limit(6)
        ->get();
        return view(
            "admin.duties.statistics",
            compact("resume", "vacancy", "curr_resume", "curr_vacancy", "percent_resume", "percent_vacancy", "rivalry"),
            compact("csat_employer", "csat_student","offers", "responses", "employments"),
        );
    }
    //навыки
    public function skillView()
    {
        $skills = DB::table('skills')->where('id', '>', 0)->pluck('id')->toArray();
        $student_skills = DB::table('student_skills')->where('skill_id', '>', 0)->pluck('skill_id')->toArray();
        $vacancy_skills = DB::table('vacancy_skills')->where('skill_id', '>', 0)->pluck('skill_id')->toArray();
        $exclude_skills = array_merge($vacancy_skills, $student_skills);
        $exclude_skills = array_unique($exclude_skills);
        $all_skills = array_diff($skills, $exclude_skills);
        $all_skills = Skill::findMany($all_skills);
        $exclude_skills = Skill::findMany($exclude_skills);
        $skills = Skill::all();
        return view('admin.duties.skills', compact('all_skills', 'skills', 'exclude_skills'));
    }
    public function editSkill(Request $request)
    {
        $skill = Skill::find($request->id);
        $skill->skill_name = $request->skill_name;
        $skill->skill_type = $request->skill_type;
        $skill->save();
    }
    public function deleteSkill(Request $request)
    {
        Skill::find($request->id)->delete();
    }
    public function addSkill(Request $request)
    {
        Skill::create([
            'skill_name' => $request->skill_name,
            'skill_type' => $request->skill_type,
        ]);
    }
    //сферы
    public function sphereView()
    {
        $spheres = DB::table('sphere_of_activities')->where('id', '>', 0)->pluck('id')->toArray();
        $subspheres = DB::table('subsphere_of_activities')->where('sphere_id', '>', 0)->pluck('sphere_id')->toArray();
        $subspheres = array_unique($subspheres);
        $exclude_spheres = array_diff($spheres, $subspheres);
        $exclude_spheres = array_unique($exclude_spheres);
        $all_spheres = array_diff($spheres, $exclude_spheres);
        $all_spheres = SphereOfActivity::findMany($all_spheres);
        $exclude_spheres = SphereOfActivity::findMany($exclude_spheres);
        $spheres = SphereOfActivity::all();
        return view('admin.duties.spheres', compact('all_spheres', 'spheres', 'exclude_spheres'));
    }
    public function editSphere(Request $request)
    {
        $sphere = SphereOfActivity::find($request->id);
        $sphere->sphere_of_activity_name = $request->sphere_of_activity_name;
        $sphere->save();
    }
    public function deleteSphere(Request $request)
    {
        SphereOfActivity::find($request->id)->delete();
    }
    public function addSphere(Request $request)
    {
        SphereOfActivity::create([
            'sphere_of_activity_name' => $request->sphere_of_activity_name,
        ]);
    }
    //подсферы
    public function subsphereView()
    {
        $subspheres = DB::table('subsphere_of_activities')->where('id', '>', 0)->pluck('id')->toArray();
        $professions = DB::table('professions')->where('subsphere_id', '>', 0)->pluck('subsphere_id')->toArray();
        $professions = array_unique($professions);
        $exclude_subspheres = array_diff($subspheres, $professions);
        $exclude_subspheres = array_unique($exclude_subspheres);
        $all_subspheres = array_diff($subspheres, $exclude_subspheres);
        $all_subspheres = SubsphereOfActivity::findMany($all_subspheres);
        $exclude_subspheres = SubsphereOfActivity::findMany($exclude_subspheres);
        $subspheres = SubsphereOfActivity::all();
        $spheres = SphereOfActivity::all();
        return view('admin.duties.subspheres', compact('subspheres', 'exclude_subspheres', 'all_subspheres', 'spheres'));
    }
    public function editSubsphere(Request $request)
    {
        $subsphere = SubsphereOfActivity::find($request->id);
        $subsphere->subsphere_of_activity_name = $request->subsphere_of_activity_name;
        $subsphere->save();
    }
    public function deleteSubsphere(Request $request)
    {
        SubsphereOfActivity::find($request->id)->delete();
    }
    public function addSubsphere(Request $request)
    {
        SubsphereOfActivity::create([
            'sphere_id' => $request->sphere_id,
            'subsphere_of_activity_name' => $request->subsphere_of_activity_name,
        ]);
    }
    //профессии
    public function professionView()
    {
        $professions = DB::table('professions')->where('id', '>', 0)->pluck('id')->toArray();
        $vacancy_professions = DB::table('vacancies')->where('profession_id', '>', 0)->pluck('profession_id')->toArray();
        $resume_professions = DB::table('resumes')->where('profession_id', '>', 0)->pluck('profession_id')->toArray();
        $exclude_professions = array_merge($vacancy_professions, $resume_professions);
        $exclude_professions = array_unique($exclude_professions);
        $all_professions = array_diff($professions, $exclude_professions);
        $all_professions = Profession::findMany($all_professions);
        $exclude_professions = Profession::findMany($exclude_professions);
        $professions = Profession::all();
        $subspheres = SubsphereOfActivity::all();
        return view('admin.duties.professions', compact('professions', 'exclude_professions', 'all_professions', 'subspheres'));
    }
    public function editProfession(Request $request)
    {
        $profession = Profession::find($request->id);
        $profession->profession_name = $request->profession_name;
        $profession->save();
    }
    public function deleteProfession(Request $request)
    {
        Profession::find($request->id)->delete();
    }
    public function addProfession(Request $request)
    {
        Profession::create([
            'subsphere_id' => $request->subsphere_id,
            'profession_name' => $request->profession_name,
        ]);
    }
}
