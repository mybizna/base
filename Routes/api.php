<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Base\Http\Controllers\ApiController;
use Modules\Base\Http\Controllers\AuthenticationController;



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


$apicontroller = 'Modules\Base\Http\Controllers\BaseController';
Route::get('discover_modules',  $apicontroller . '@discoverModules');
Route::get('fetch_menus', $apicontroller . '@fetchMenus');
Route::get('fetch_routes', $apicontroller . '@fetchRoutes');
Route::get('fetch_settings', $apicontroller . '@fetchSettings');


Route::middleware('auth:sanctum')->group(function () {
    $apicontroller = 'Modules\Base\Http\Controllers\BaseController';

    Route::get('autocomplete', $apicontroller . '@autocomplete');
    Route::get('current_user',  $apicontroller . '@currentUser');
    Route::get('dashboard_data',  $apicontroller . '@dashboardData');
    Route::get('profile',  $apicontroller . '@profile');
});

Route::middleware('auth:sanctum')->group(function () {
    $prefix = '{module}/admin/{model}';

    $apicontroller = 'Modules\Base\Http\Controllers\BaseController';

    Route::get($prefix, $apicontroller . '@getAllRecords');
    Route::get($prefix . '/{id}', $apicontroller . '@getRecord')->where(['id' => '[0-9]+']);
    Route::get($prefix . '/recordselect', $apicontroller . '@getRecordSelect');
    Route::post($prefix, $apicontroller . '@createRecord');
    Route::post($prefix . '/{id}', $apicontroller . '@updateRecord')->where(['id' => '[0-9]+']);
    Route::delete($prefix . '/{id}', $apicontroller . '@deleteRecord')->where(['id' => '[0-9]+']);
    Route::match(['get', 'post'], $prefix . '/{function}/',  $apicontroller . '@functionCall');
});


