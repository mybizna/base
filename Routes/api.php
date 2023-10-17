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

$rights = ['logged_in'];
Route::get('autocomplete', [GeneralController::class, 'autocomplete'], )->rights($rights);
Route::get('current_user', [GeneralController::class, 'currentUser'], )->rights($rights);
Route::get('dashboard_data', [GeneralController::class, 'dashboardData'], )->rights($rights);
Route::get('profile', [GeneralController::class, 'profile'], )->rights($rights);

$prefix = '{module}/{model}';

Route::get($prefix, [BaseController::class, 'getAllRecords'])->rights($rights);
Route::get($prefix . '/{id}', [BaseController::class, 'getRecord'])->rights($rights)->where(['id' => '[0-9]+']);
Route::get($prefix . '/recordselect', [BaseController::class, 'getRecordSelect'])->rights($rights);
Route::post($prefix, [BaseController::class, 'createRecord'])->rights($rights);
Route::post($prefix . '/{id}', [BaseController::class, 'updateRecord'])->rights($rights)->where(['id' => '[0-9]+']);
Route::delete($prefix . '/{id}', [BaseController::class, 'deleteRecord'])->rights($rights)->where(['id' => '[0-9]+']);
