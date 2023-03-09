<?php

use App\Http\Controllers\AddEmployerController;
use App\Http\Controllers\AddStudentController;
use App\Http\Controllers\AddUniversityController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest'], 'prefix' => 'student', 'as' => 'student.'], function () {
	Route::get('/password-link/{token}', [AddStudentController::class, 'showStudentPasswordSetter'])->name('password-link');
	Route::post('/password-link/{token}', [AddStudentController::class, 'setPassword'])->name('password-link');
});

Route::group(['middleware' => ['guest'], 'prefix' => 'university', 'as' => 'university.'], function () {
	Route::get('/password-link/{token}', [AddUniversityController::class, 'showUniversityPasswordSetter'])->name('password-link');
	Route::post('/password-link/{token}', [AddUniversityController::class, 'setPassword'])->name('password-link');
});

Route::group(['middleware' => ['guest'], 'prefix' => 'employer', 'as' => 'employer.'], function () {
	Route::get('/password-link/{token}', [AddEmployerController::class, 'showEmployerPasswordSetter'])->name('password-link');
	Route::post('/password-link/{token}', [AddEmployerController::class, 'setPassword'])->name('password-link');
});