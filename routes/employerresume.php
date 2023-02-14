<?php

use App\Http\Controllers\Employer\EmployerInvitationsController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ResumeFeedController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:employer'], 'prefix' => 'employer', 'as' => 'employer.'], function () {
	Route::get('/resume-feed', [ResumeFeedController::class, 'index'])->name('resume-feed');
	Route::get('/resume/{id}', [ResumeFeedController::class, 'displayResumeDetails'])->name('resume');


	Route::post('/rate-a-student', [RateController::class, 'postStudentRate'])->name('rate-a-student');
	Route::get('/student-rate-page', [RateController::class, 'studentRatePage'])->name('student-rate-page');

	Route::post('/edit-student-rate', [RateController::class, 'editStudentRate'])->name('edit-student-rate');
	Route::get('/student-rate-page-edit', [RateController::class, 'studentRatePageEdit'])->name('student-rate-page-edit');

});
