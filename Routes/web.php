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
use Illuminate\Support\Facades\Auth;
use Modules\Base\Http\Controllers\BaseController;

//manage new user
Route::get('/manage', [BaseController::class, 'manage']);

require_once 'auth.php';

