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
use Modules\Base\Http\Controllers\GeneralController;

//manage new user
Route::get('/manage', [GeneralController::class, 'manage']);

Route::get('fetch_vue/{section}', [GeneralController::class, 'fetchVue'])->where(['section' => '.*']);
Route::get('fetch_layout/{module}/{model}/{action}', [GeneralController::class, 'fetchLayout'])->where(['module' => '.*', 'model' => '.*']);

Route::get('base/clear-cache', [GeneralController::class, 'clearCache']);
Route::get('base/route-cache', [GeneralController::class, 'routeCache']);
Route::get('base/config-cache', [GeneralController::class, 'configCache']);
Route::get('base/view-clear', [GeneralController::class, 'viewClear']);

Route::get('base/automigrator-migrate', [GeneralController::class, 'automigratorMigrate']);
Route::match (['get', 'post'], 'base/mybizna-dataprocessor', [GeneralController::class, 'dataProcessor']);
Route::get('base/create-user', [GeneralController::class, 'createUser'])->name('create-user');
Route::get('base/reset-all', [GeneralController::class, 'resetAll']);

require_once 'auth.php';
