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

Route::group(['namespace' => $namespace], function () use ($options, $namespace) {
    // Login Routes...
    if ($options['login'] ?? true) {
        Route::get('login', $namespace . 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('login', $namespace . 'Auth\LoginController@login');
    }

    // Logout Routes...
    if ($options['logout'] ?? true) {
        Route::post('logout', $namespace . 'Auth\LoginController@logout')->name('logout');
    }

    // Registration Routes...
    if ($options['register'] ?? true) {
        Route::get('register', $namespace . 'Auth\RegisterController@showRegistrationForm')->name('register');
        Route::post('register', $namespace . 'Auth\RegisterController@register');
    }

    // Password Reset Routes...
    if ($options['reset'] ?? true) {
        resetPassword($namespace);
    }

    // Password Confirmation Routes...
    if ($options['confirm'] ??
        class_exists($namespace . $namespace . 'Auth\ConfirmPasswordController')) {
        confirmPassword($namespace);
    }

    // Email Verification Routes...
    if ($options['verify'] ?? false) {
        emailVerification($namespace);
    }
});

/**
 * Register the typical reset password routes for an application.
 *
 * @return callable
 */
function resetPassword()
{
    Route::get('password/reset', $namespace . 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', $namespace . 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', $namespace . 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', $namespace . 'Auth\ResetPasswordController@reset')->name('password.update');
}

/**
 * Register the typical confirm password routes for an application.
 *
 * @return callable
 */
function confirmPassword()
{
    Route::get('password/confirm', $namespace . 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
    Route::post('password/confirm', $namespace . 'Auth\ConfirmPasswordController@confirm');
}

/**
 * Register the typical email verification routes for an application.
 *
 * @return callable
 */
function emailVerification()
{
    Route::get('email/verify', $namespace . 'Auth\VerificationController@show')->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', $namespace . 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', $namespace . 'Auth\VerificationController@resend')->name('verification.resend');
}
