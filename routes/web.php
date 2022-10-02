<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;

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

Route::middleware('web')->group(function(){
    Route::get('/', [AppController::class, 'index']);
    Route::post('/', [AppController::class, 'index']);

    Route::get('login', [LoginController::class, 'Login']);
    Route::post('login', [LoginController::class, 'Login']);
    Route::get('menu/{peran_id}', [LoginController::class, '_menu']);
    Route::get('menu_portal', [LoginController::class, '_menu_portal']);
    Route::get('getSliders', [AppController::class, 'getSliders']);

    Route::get('login/{password}/{email}/login', [LoginController::class, 'Login']);
    Route::get('cek_password', [LoginController::class, 'password_cek']);

    Route::options('{any}', function($any){ return Response('OK', 200); });
    Route::options('{a}/{b}', function($a, $b){ return Response('OK', 200); });
    Route::options('{a}/{b}/{c}', function($a,$b,$c){ return Response('OK', 200); });
    Route::options('{a}/{b}/{c}/{d}', function($a,$b,$c,$d){ return Response('OK', 200); });
    Route::options('{a}/{b}/{c}/{d}/{e}', function($a,$b,$c,$d,$e){ return Response('OK', 200); });
});


Route::options('{any}', function($any){ return Response('OK', 200); });
Route::options('{a}/{b}', function($a, $b){ return Response('OK', 200); });
Route::options('{a}/{b}/{c}', function($a,$b,$c){ return Response('OK', 200); });
Route::options('{a}/{b}/{c}/{d}', function($a,$b,$c,$d){ return Response('OK', 200); });
Route::options('{a}/{b}/{c}/{d}/{e}', function($a,$b,$c,$d,$e){ return Response('OK', 200); });