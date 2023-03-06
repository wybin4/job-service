<?php

use App\Http\Controllers\AddStudentController;
use App\Http\Controllers\University\UniversityDuties;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:university'], 'prefix' => 'university', 'as' => 'university.'], function () {
	Route::get('/add-one', [AddStudentController::class, 'openAddOne']);
	Route::post('/add-one', [AddStudentController::class, 'addOneStudent']);
	Route::get('/add-many', [AddStudentController::class, 'openAddMany']);
	Route::post('/add-many', [AddStudentController::class, 'addManyStudents']);
	Route::get('/statistics', [UniversityDuties::class, 'viewStatictics']);
	Route::get('/total-statistics', [UniversityDuties::class, 'viewTotalStatistics']);
});
