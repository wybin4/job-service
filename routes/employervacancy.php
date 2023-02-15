<?php

use App\Http\Controllers\Employer\VacancyOffersController;
use App\Http\Controllers\Employer\VacancyResponsesController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\SphereOfActivity;
use App\Models\SubsphereOfActivity;
use App\Models\Profession;
use App\Models\TypeOfEmployment;
use App\Models\WorkType;
use App\Models\Skill;

Route::group(['middleware' => ['auth:employer'], 'prefix' => 'employer', 'as' => 'employer.'], function () {
	Route::get('/create-vacancy', function () {
		$sphere = SphereOfActivity::all();
		$category = SubsphereOfActivity::all();
		$profession = Profession::all();
		$type_of_employment = TypeOfEmployment::all();
		$work_type = WorkType::all();
		$skill = Skill::all();
		return view('employer.vacancy.create-vacancy')
			->with('sphere', $sphere)
			->with('category', $category)
			->with('profession', $profession)
			->with('type_of_employment', $type_of_employment)
			->with('work_type', $work_type)
			->with('skill', $skill);
	})->name('create-vacancy');
	Route::post('/create-vacancy', [VacancyController::class, 'createVacancy']);
	Route::get('/vacancy-details/{id}', [VacancyController::class, 'vacancyDetails'])->name('vacancy-details');
	Route::get('/find-candidates/{id}', [VacancyController::class, 'findCandidates'])->name('find-candidates');
	Route::get('/edit-vacancy/{id}', [VacancyController::class, 'editVacancyPage'])->name('edit-vacancy');
	Route::post('/edit-vacancy', [VacancyController::class, 'editVacancy'])->name('edit-vacancy');

	Route::get('/vacancies-list', [VacancyController::class, 'vacanciesList'])->name('vacancies-list');
	Route::post('/add-skill', [VacancyController::class, 'addSkill'])->name('add-skill');
	Route::post('/add-profession', [VacancyController::class, 'addProfession'])->name('add-profession');

	Route::post('/archive-vacancy', [VacancyController::class, 'archiveVacancy'])->name('archive-vacancy');
	Route::post('/unarchive-vacancy', [VacancyController::class, 'unarchiveVacancy'])->name('unarchive-vacancy');

	Route::get('/vacancy-responses/{id}', [VacancyResponsesController::class, 'vacancyResponses'])->name('vacancy-responses');
	Route::get('/all-vacancy-responses', [VacancyResponsesController::class, 'allVacancyResponses'])->name('all-vacancy-responses');
	Route::post('/change-status', [VacancyResponsesController::class, 'changeStatus'])->name('change-status');
	Route::get('/student-interaction', [VacancyResponsesController::class, 'totalView'])->name('student-interaction');

	Route::post('/send-offer', [VacancyOffersController::class, 'sendOffer'])->name('send-offer');
	Route::get('/vacancy-offers/{id}', [VacancyOffersController::class, 'vacancyOffers'])->name('vacancy-offers');
	Route::get('/all-vacancy-offers', [VacancyOffersController::class, 'allOffers'])->name('all-vacancy-offers');
});
