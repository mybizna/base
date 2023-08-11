<?php

use Illuminate\Support\Facades\Route;
use Modules\Base\Http\Controllers\AuthenticationController;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Base\Http\Controllers\GeneralController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//register new user
Route::post('/register', [AuthenticationController::class, 'register']);

//login user
Route::post('/login', [AuthenticationController::class, 'login']);
Route::get('/autologin', [AuthenticationController::class, 'autologin']);

Route::get('discover_modules', [GeneralController::class, 'discoverModules']);
Route::get('fetch_menus', [GeneralController::class, 'fetchMenus']);
Route::get('fetch_routes', [GeneralController::class, 'fetchRoutes']);
Route::get('fetch_positions', [GeneralController::class, 'fetchPositions']);
Route::get('fetch_rights', [GeneralController::class, 'fetchRights']);
Route::get('fetch_settings', [GeneralController::class, 'fetchSettings']);
Route::get('fetch_layout/{module}/{model}/{action}', [GeneralController::class, 'fetchLayout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('autocomplete', [GeneralController::class, 'autocomplete']);
    Route::get('current_user', [GeneralController::class, 'currentUser']);
    Route::get('dashboard_data', [GeneralController::class, 'dashboardData']);
    Route::get('profile', [GeneralController::class, 'profile']);
});

Route::middleware('auth:sanctum')->group(function () {
    $prefix = '{module}/admin/{model}';

    Route::get($prefix, [BaseController::class, 'getAllRecords']);
    Route::get($prefix . '/{id}', [BaseController::class, 'getRecord'])->where(['id' => '[0-9]+']);
    Route::get($prefix . '/recordselect', [BaseController::class, 'getRecordSelect']);
    Route::post($prefix, [BaseController::class, 'createRecord']);
    Route::post($prefix . '/{id}', [BaseController::class, 'updateRecord'])->where(['id' => '[0-9]+']);
    Route::delete($prefix . '/{id}', [BaseController::class, 'deleteRecord'])->where(['id' => '[0-9]+']);
});
