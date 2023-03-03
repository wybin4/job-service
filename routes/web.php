<?php

use App\Http\Controllers\AddEmployerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddStudentController;
use App\Http\Controllers\AddUniversityController as ControllersAddUniversityController;
use App\Http\Controllers\Location;
use App\Models\Skill;
use App\Http\Controllers\Admin\AdminDuties;
use App\Http\Controllers\University\UniversityDuties;
use App\Models\Employer;
use App\Models\Resume;
use App\Models\SphereOfActivity;
use App\Models\Student;
use App\Models\University;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');
require __DIR__ . '/auth.php';

/////


Route::get('/university/dashboard', function () {
    return view('university.dashboard');
})->middleware(['auth:university'])->name('university.dashboard');
require __DIR__ . '/universityauth.php';
Route::get('/university/add-one', [AddStudentController::class, 'openAddOne']);
Route::post('/university/add-one', [AddStudentController::class, 'addOneStudent']);
Route::get('/university/add-many', [AddStudentController::class, 'openAddMany']);
Route::post('/university/add-many', [AddStudentController::class, 'addManyStudents']);
Route::get('/university/statistics', [UniversityDuties::class, 'viewStatictics']);
Route::group(['middleware' => ['guest']], function () {
    Route::get('/university/password-link/{token}', [ControllersAddUniversityController::class, 'showUniversityPasswordSetter'])->name('university.password-link');
    Route::post('/university/password-link/{token}', [ControllersAddUniversityController::class, 'setPassword'])->name('university.password-link');
});

/////

Route::group(['middleware' => ['guest']], function () {
    Route::get('/student/password-link/{token}', [AddStudentController::class, 'showStudentPasswordSetter'])->name('student.password-link');
    Route::post('/student/password-link/{token}', [AddStudentController::class, 'setPassword'])->name('student.password-link');
});
Route::get('/student/dashboard', function () {
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
})->middleware(['auth:student'])->name('student.dashboard');
Route::post('/student/add-location', [Location::class, 'addStudentLocation'])
    ->middleware(['auth:student'])
    ->name('student.add-location');
//уведомления
Route::post('/student/mark-as-read', function (Request $request) {
    Student::find($request->student_id)->unreadNotifications->where('id', $request->id)->markAsRead();
})->middleware(['auth:student'])->name('student.mark-as-read');
require __DIR__ . '/studentauth.php';
require __DIR__ . '/studentprofile.php';
require __DIR__ . '/studentresume.php';
require __DIR__ . '/studentvacancy.php';


////

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth:admin'])->name('admin.dashboard');
require __DIR__ . '/adminauth.php';
require __DIR__ . '/adminduties.php';
Route::get('/admin/add-one-employer', [AddEmployerController::class, 'openAddOne']);
Route::post('/admin/add-one-employer', [AddEmployerController::class, 'addOneEmployer']);
Route::get('/admin/add-one-university', [ControllersAddUniversityController::class, 'openAddOne']);
Route::post('/admin/add-one-university', [ControllersAddUniversityController::class, 'addOneUniversity']);

///////


Route::get('/employer/dashboard', function () {
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
})->middleware(['auth:employer'])->name('employer.dashboard');
require __DIR__ . '/employerauth.php';
Route::post('/employer/add-location', [Location::class, 'addEmployerLocation'])->name('employer.add-location');
Route::group(['middleware' => ['guest']], function () {
    Route::get('/employer/password-link/{token}', [AddEmployerController::class, 'showEmployerPasswordSetter'])->name('employer.password-link');
    Route::post('/employer/password-link/{token}', [AddEmployerController::class, 'setPassword'])->name('employer.password-link');
});
//уведомления
Route::post('/employer/mark-as-read', function (Request $request) {
    Employer::find($request->employer_id)->unreadNotifications->where('id', $request->id)->markAsRead();
})->middleware(['auth:employer'])->name('employer.mark-as-read');

require __DIR__ . '/employerprofile.php';
require __DIR__ . '/employervacancy.php';
require __DIR__ . '/employerresume.php';


