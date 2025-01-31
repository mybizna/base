<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
use Illuminate\Support\Facades\Route;

use Modules\Base\Http\Controllers\Auth\LoginController;
use Modules\Base\Http\Controllers\Auth\ForgotPasswordController;
use Modules\Base\Http\Controllers\Auth\RegisterController;
use Modules\Base\Http\Controllers\Auth\ResetPasswordController;
use Modules\Base\Http\Controllers\Auth\VerificationController;
use Modules\Base\Http\Controllers\Auth\ConfirmPasswordController;

//manage new user
$options = [];
$namespace = "Modules\Base\Http\Controllers";

// Login Routes...
if ($options['login'] ?? true) {

    Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class,'login']);
}

// Logout Routes...
if ($options['logout'] ?? true) {
    Route::post('logout', [LoginController::class,'logout'])->name('logout');
}

// Registration Routes...
if ($options['register'] ?? true) {
    Route::get('register', [RegisterController::class,'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class,'register']);
}

// Password Reset Routes...
if ($options['reset'] ?? true) {
    resetPassword();
}

// Password Confirmation Routes...
if ($options['confirm'] ??
    class_exists('Auth\ConfirmPasswordController')) {
    confirmPassword();
}

// Email Verification Routes...
if ($options['verify'] ?? false) {
    emailVerification();
}

/**
 * Register the typical reset password routes for an application.
 *
 * @return callable
 */
function resetPassword()
{
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ForgotPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ForgotPasswordController::class,'reset'])->name('password.update');
}

/**
 * Register the typical confirm password routes for an application.
 *
 * @return callable
 */
function confirmPassword()
{
    Route::get('password/confirm', [ConfirmPasswordController::class,'showConfirmForm'])->name('password.confirm');
    Route::post('password/confirm', [ConfirmPasswordController::class,'confirm']);
}

/**
 * Register the typical email verification routes for an application.
 *
 * @return callable
 */
function emailVerification()
{
    Route::get('email/verify', [VerificationController::class,'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class,'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class,'resend'])->name('verification.resend');
}
