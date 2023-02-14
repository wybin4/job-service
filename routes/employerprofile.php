<?php

use App\Http\Controllers\Employer\EmployerDuties;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:employer'], 'prefix' => 'employer', 'as' => 'employer.'], function () {
	Route::get('/alter-profile', [EmployerDuties::class, 'viewAlterProfilePage'])->name('alter-profile');
	Route::post('/alter-profile', [EmployerDuties::class, 'alterProfile'])->name('alter-profile');
	Route::post('/alter-password', [EmployerDuties::class, 'alterPassword'])->name('alter-password');
});
