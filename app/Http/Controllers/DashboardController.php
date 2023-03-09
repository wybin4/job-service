<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\SphereOfActivity;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function studentDashboard()
    {
        $popular_professions = Vacancy::where('status', 0)
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->select(DB::raw('count(*) as profession_name_count, profession_name'))
            ->groupBy('profession_name')
            ->orderBy('profession_name_count', 'desc')
            ->paginate(3);
        $spheres_with_count = Vacancy::where('status', 0)
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->join('subsphere_of_activities', 'subsphere_of_activities.id', '=', 'professions.subsphere_id')
            ->join('sphere_of_activities', 'sphere_of_activities.id', '=', 'subsphere_of_activities.sphere_id')
            ->select(DB::raw('count(*) as sphere_of_activities_count, sphere_of_activity_name'))
            ->groupBy('sphere_of_activity_name')
            ->get();
        $spheres = SphereOfActivity::all();
        return view('student.dashboard', compact('popular_professions', 'spheres_with_count', 'spheres'));
    }
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }
    public function universityDashboard()
    {
        return view('university.dashboard');
    }
    public function employerDashboard()
    {
        $popular_professions = Resume::where('status', 0)
            ->join('professions', 'professions.id', '=', 'resumes.profession_id')
            ->select(DB::raw('count(*) as profession_name_count, profession_name'))
            ->groupBy('profession_name')
            ->orderBy('profession_name_count', 'desc')
            ->paginate(3);
        $spheres_with_count = Resume::where('status', 0)
            ->join('professions', 'professions.id', '=', 'resumes.profession_id')
            ->join('subsphere_of_activities', 'subsphere_of_activities.id', '=', 'professions.subsphere_id')
            ->join('sphere_of_activities', 'sphere_of_activities.id', '=', 'subsphere_of_activities.sphere_id')
            ->select(DB::raw('count(*) as sphere_of_activities_count, sphere_of_activity_name'))
            ->groupBy('sphere_of_activity_name')
            ->get();
        $spheres = SphereOfActivity::all();
        return view('employer.dashboard', compact('popular_professions', 'spheres_with_count', 'spheres'));
    }
}
