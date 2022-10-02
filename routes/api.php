<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TasksController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/users/{user}', [UserController::class, 'show']);

Route::prefix('tasks')->group(function(){
    Route::get('get', [TasksController::class, 'get']);
});

Route::middleware('token')->group(function(){
    Route::get('/', [AppController::class, 'index']);
    Route::post('/', [AppController::class, 'index']);
    
    Route::prefix('auth')->group(function () {
        Route::get('token', [LoginController::class, 'CekToken']);  
        
        Route::post('register', function(){
            return Response('OK', 200);
        });
        Route::post('user/update', function(){
            return Response('OK', 200);
        });
    });

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return "Cache is cleared";
    });

    Route::post('get_sessions', function(){
        return ['status' => 'get_cache_browser'];
    });

    Route::prefix('app')->group(function () {
        Route::post('wilayah', [AppController::class, 'Wilayah']);
        Route::post('optWilayah', [AppController::class, 'optWilayah']);
        Route::post('get_menu_item', [AppController::class, 'GetMemuItem']);
        Route::get('get_widgets', [AppController::class, 'getWidget']);
        Route::get('getSlider', [AppController::class, 'getSlider']);
        Route::post('simpan_slider', [AppController::class, 'simpan_slider']);    
    });
    
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