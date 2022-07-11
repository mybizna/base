<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthenticationController;



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


$apicontroller = 'Modules\Base\Http\Controllers\BaseController';
Route::get('discover_modules',  $apicontroller . '@discoverModules');
Route::get('fetch_menus', $apicontroller . '@fetchMenus');
Route::get('fetch_routes', $apicontroller . '@fetchRoutes');



Route::middleware('auth:sanctum')->group(function () {
    $apicontroller = 'Modules\Base\Http\Controllers\BaseController';

    Route::get('autocomplete', $apicontroller . '@autocomplete');
    Route::get('current_user',  $apicontroller . '@currentUser');
    Route::get('dashboard_data',  $apicontroller . '@dashboardData');
});

Route::middleware('auth:sanctum')->group(function () {
    $prefix = '{module}/admin/{model}';

    $apicontroller = 'Modules\Base\Http\Controllers\BaseController';

    Route::get($prefix, $apicontroller . '@getAllRecords');
    Route::get($prefix . '/{id}', $apicontroller . '@getRecord');
    Route::get($prefix . '/recordselect', $apicontroller . '@getRecordSelect');
    Route::post($prefix, $apicontroller . '@createRecord');
    Route::put($prefix . '/{id}', $apicontroller . '@updateRecord');
    Route::delete($prefix . '/{id}', $apicontroller . '@deleteRecord');
    Route::match(['get', 'post'], $prefix . '/{function}/',  $apicontroller . '@functionCall');
});


/***
 *
 *
    $DS = DIRECTORY_SEPARATOR;
    $modules_path = realpath(base_path()) . $DS . 'Modules';

    if (is_dir($modules_path)) {
        $dir = new \DirectoryIterator($modules_path);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                $module_name = $fileinfo->getFilename();
                $camel_module_name = ucfirst(Str::camel($module_name));
                $snake_module_name = Str::lower(Str::snake($module_name));

                $entities_path = $modules_path . $DS . $module_name . $DS . 'Entities';

                if (is_dir($entities_path)) {
                    $dir = new \DirectoryIterator($entities_path);

                    foreach ($dir as $fileinfo) {
                        if (!$fileinfo->isDir()) {
                            $entity_name = $fileinfo->getFilename();
                            $entity_name_arr = explode('.', $entity_name);
                            $camel_entity_name = ucfirst(Str::camel($entity_name_arr[0]));
                            $snake_entity_name = Str::lower(Str::snake($entity_name_arr[0]));

                            $controller_path = realpath(base_path()) . $DS . 'Modules' . $DS . $camel_module_name . $DS . 'Http' . $DS . 'Controllers' . $DS . $camel_entity_name . 'Controller.php';
                            $controller = 'Modules\\' . $camel_module_name . '\Http\Controllers\\' . $camel_entity_name . 'Controller';
                            $prefix = $snake_module_name . '/admin/' . $snake_entity_name;


                            if (file_exists($controller_path)) {
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::get($prefix, $controller . '@getAllRecords');
                                }
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::get($prefix . '/{id}', $controller . '@getRecord');
                                }
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::get($prefix . '/recordselect', $controller . '@getRecordSelect');
                                }
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::post($prefix, $controller . '@createRecord');
                                }
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::put($prefix . '/{id}', $controller . '@updateRecord');
                                }
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::delete($prefix . '/{id}', $controller . '@deleteRecord');
                                }
                                if (method_exists($controller, 'getAllRecords')) {
                                    Route::match(['get', 'post'], $prefix . '/{function}//',  $controller . '@functionCall');
                                }
                            }
                        }
                    }
                }
            }
        }
    }
 */
