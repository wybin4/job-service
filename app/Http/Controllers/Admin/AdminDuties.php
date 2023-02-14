<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\SphereOfActivity;
use App\Models\SubsphereOfActivity;
use Illuminate\Support\Facades\DB;

class AdminDuties extends Controller
{
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
