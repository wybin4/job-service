<?php

use App\Http\Controllers\Universityauth\AuthenticatedSessionController;
use App\Http\Controllers\Universityauth\ConfirmablePasswordController;
use App\Http\Controllers\Universityauth\EmailVerificationNotificationController;
use App\Http\Controllers\Universityauth\EmailVerificationPromptController;
use App\Http\Controllers\Universityauth\NewPasswordController;
use App\Http\Controllers\Universityauth\PasswordResetLinkController;
use App\Http\Controllers\Universityauth\RegisteredUserController;
use App\Http\Controllers\Universityauth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest:university'], 'prefix' => 'university', 'as' => 'university.'], function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::group(['middleware' => ['auth:university'], 'prefix' => 'university', 'as' => 'university.'], function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
