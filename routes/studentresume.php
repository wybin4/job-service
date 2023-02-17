<?php

use App\Http\Controllers\ResumeController;
use App\Http\Controllers\Student\ResumeInvivationsController;
use App\Http\Controllers\Student\StudentResponsesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\SphereOfActivity;
use App\Models\SubsphereOfActivity;
use App\Models\Profession;
use App\Models\TypeOfEmployment;
use App\Models\WorkType;
use App\Models\Skill;

Route::group(['middleware' => ['auth:student'], 'prefix' => 'student', 'as' => 'student.'], function () {
	Route::get('/create-resume', function () {
		$sphere = SphereOfActivity::all();
		$category = SubsphereOfActivity::all();
		$profession = Profession::all();
		$type_of_employment = TypeOfEmployment::all();
		$work_type = WorkType::all();
		$skill = Skill::all();
		return view('student.resume.create-resume')
		->with('sphere', $sphere)
			->with('category', $category)
			->with('profession', $profession)
			->with('type_of_employment', $type_of_employment)
			->with('work_type', $work_type)
			->with('skill', $skill);
	})->name('create-resume');
	Route::get('/alter-resume', function () {
		if (Auth::guard('student')->user()->resume) {
			$sphere = SphereOfActivity::all();
			$category = SubsphereOfActivity::all();
			$profession = Profession::all();
			$type_of_employment = TypeOfEmployment::all();
			$work_type = WorkType::all();
			$resume = Auth::User()->resume;
			return view('student.resume.alter-resume')
			->with('sphere', $sphere)
				->with('category', $category)
				->with('profession', $profession)
				->with('type_of_employment', $type_of_employment)
				->with('work_type', $work_type)
				->with('resume', $resume);
		}
	})->name('alter-resume');
	Route::post('/create-resume', [ResumeController::class, 'createResume'])->name('create-resume');
	Route::get('/resume/{id}', [ResumeController::class, 'resumeDetails'])->name('resume');

	Route::get('/edit-resume/{id}', [ResumeController::class, 'editResumePage'])->name('edit-resume');
	Route::post('/edit-resume', [ResumeController::class, 'editResume'])->name('edit-resume');

	Route::get('/archive-resume', [ResumeController::class, 'archiveResume'])->name('archive-resume');
	Route::get('/archived-resumes-feed', [ResumeController::class, 'viewArchivedResumesPage'])->name('archived-resumes-feed');
	Route::post('/unarchive-resume', [ResumeController::class, 'unarchiveResume'])->name('unarchive-resume');

	Route::post('/add-skill', [ResumeController::class, 'addSkill'])->name('add-skill');
	Route::post('/add-profession', [ResumeController::class, 'addProfession'])->name('add-profession');
	Route::post('/add-experience', [ResumeController::class, 'addExperience'])->name('add-experience');
});