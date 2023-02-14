<?php

use App\Http\Controllers\Student\StudentOffersController;
use App\Http\Controllers\Student\StudentResponsesController;
use App\Http\Controllers\VacancyFeedController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:student'], 'prefix' => 'student', 'as' => 'student.'], function () {
	Route::get('/vacancy-feed', [VacancyFeedController::class, 'index'])->name('vacancy-feed');
	Route::get('/vacancy/{id}', [VacancyFeedController::class, 'displayVacancyDetails'])->name('vacancy');
	Route::get('/employer/{id}', [VacancyFeedController::class, 'displayEmployerDetails'])->name('employer');

	Route::post('/student-response', [StudentResponsesController::class, 'postResponse'])->name('student-response');
	Route::get('/my-responses', [StudentResponsesController::class, 'myResponses'])->name('my-responses');
	Route::get('/employer-interaction', [StudentResponsesController::class, 'totalView'])->name('employer-interaction');

	Route::get('/all-offers', [StudentOffersController::class, 'allOffers'])->name('all-offers');
	Route::post('/change-status', [StudentOffersController::class, 'changeStatus'])->name('change-status');
});
