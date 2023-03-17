<?php

use App\Http\Controllers\AddEmployerController;
use App\Http\Controllers\AddUniversityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Location;
use App\Http\Controllers\NotificationsController;


Route::get('/', function () {
    return view('welcome');
});

/////

require __DIR__ . '/universityauth.php';
require __DIR__ . '/universityduties.php';


/////



Route::group(['middleware' => ['auth:student'], 'prefix' => 'student', 'as' => 'student.'], function () {
    Route::post('/add-location', [Location::class, 'addStudentLocation'])->name('add-location');
    Route::post('/mark-as-read', [NotificationsController::class, 'studentMarkAsRead'])->name('mark-as-read');
});
require __DIR__ . '/studentauth.php';
require __DIR__ . '/studentprofile.php';
require __DIR__ . '/studentresume.php';
require __DIR__ . '/studentvacancy.php';


////

Route::group(['middleware' => ['auth:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/add-one-employer', [AddEmployerController::class, 'openAddOne'])->name('add-one-employer');
    Route::post('/add-one-employer', [AddEmployerController::class, 'addOneEmployer'])->name('add-one-employer');
    Route::get('/add-one-university', [AddUniversityController::class, 'openAddOne'])->name('add-one-university');
    Route::post('/add-one-university', [AddUniversityController::class, 'addOneUniversity'])->name('add-one-university');
});
require __DIR__ . '/adminauth.php';
require __DIR__ . '/adminduties.php';

///////


Route::group(['middleware' => ['auth:employer'], 'prefix' => 'employer', 'as' => 'employer.'], function () {
    Route::post('/add-location', [Location::class, 'addEmployerLocation'])->name('add-location');
    Route::post('/mark-as-read', [NotificationsController::class, 'employerMarkAsRead'])->name('mark-as-read');
});
require __DIR__ . '/employerauth.php';
require __DIR__ . '/employerprofile.php';
require __DIR__ . '/employervacancy.php';
require __DIR__ . '/employerresume.php';


require __DIR__ . '/setpassword.php';
require __DIR__ . '/dashboards.php';
