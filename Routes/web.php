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
use Modules\Base\Classes\Datasetter;
use Modules\Base\Http\Controllers\BaseController;
use Mybizna\Automigrator\Commands\MigrateCommand;

$apicontroller = 'Modules\Base\Http\Controllers\BaseController';
Route::get('fetch_vue/{section}', [BaseController::class, 'fetchVue'])->where(['section' => '.*']);

//manage new user
Route::get('/manage', [BaseController::class, 'manage']);

// Clear application cache:
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Application cache has been cleared';
});

//Clear route cache:
Route::get('/route-cache', function () {
    Artisan::call('route:cache');
    return 'Routes cache has been cleared';
});

//Clear config cache:
Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return 'Config cache has been cleared';
});

// Clear view cache:
Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'View cache has been cleared';
});

// Clear view cache:
Route::get('/automigrator-migrate', function () {
    $MigrateCommand = new MigrateCommand();
    $MigrateCommand->migrateModels();
    return 'View cache has been cleared';
});

// Clear view cache:
Route::get('/mybizna-dataprocessor', function () {
    $Datasetter = new Datasetter();
    $Datasetter->dataProcess();
    return 'View cache has been cleared';
});

Route::get('/reset-all', function () {

    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return 'Clear all reset.';
});

require_once 'auth.php';
