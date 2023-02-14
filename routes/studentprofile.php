<?php

use App\Http\Controllers\Student\StudentDuties;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:student'], 'prefix' => 'student', 'as' => 'student.'], function () {
	Route::get('/alter-profile', [StudentDuties::class, 'viewAlterProfilePage'])->name('alter-profile');
	Route::post('/alter-profile', [StudentDuties::class, 'alterProfile'])->name('alter-profile');
	Route::post('/alter-password', [StudentDuties::class, 'alterPassword'])->name('alter-password');
});
