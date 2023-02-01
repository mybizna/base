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

//manage new user
$options = [];
$namespace = 'Modules\Base\Http\Controllers\\';

// Login Routes...
if ($options['login'] ?? true) {
 
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
}

// Logout Routes...
if ($options['logout'] ?? true) {
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
}

// Registration Routes...
if ($options['register'] ?? true) {
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');
}

// Password Reset Routes...
if ($options['reset'] ?? true) {
    resetPassword($namespace);
}

// Password Confirmation Routes...
if ($options['confirm'] ??
    class_exists('Auth\ConfirmPasswordController')) {
    confirmPassword($namespace);
}

// Email Verification Routes...
if ($options['verify'] ?? false) {
    emailVerification($namespace);
}

/**
 * Register the typical reset password routes for an application.
 *
 * @return callable
 */
function resetPassword($namespace)
{
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
}

/**
 * Register the typical confirm password routes for an application.
 *
 * @return callable
 */
function confirmPassword($namespace)
{
    Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
    Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');
}

/**
 * Register the typical email verification routes for an application.
 *
 * @return callable
 */
function emailVerification($namespace)
{
    Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
}
