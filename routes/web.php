<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProdukHukumController;
use App\Http\Controllers\KebijakanController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\TentangUKSController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\StratifikasiuksController;
use App\Http\Controllers\PraktekbaikController;
use App\Http\Controllers\AplikasiController;
use App\Http\Controllers\PublikasiController;

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

    Route::prefix('Pengguna')->group(function(){
        Route::get('getPengawas', [PenggunaController::class, 'getpengawas']);
        Route::get('getSekolah', [PenggunaController::class, 'getSekolah']);
    });
    

    Route::get('cView/{fileId}', [ProdukHukumController::class, 'cView']);
    Route::get('cUnduh/{fileId}', [ProdukHukumController::class, 'cUnduh']);

    Route::get('get_berita/{page}', [BeritaController::class, 'get_berita']);
    Route::get('get_produk_hukum/{page}', [ProdukHukumController::class, 'get_produk_hukum']);
    Route::get('get_kebijakan/{page}', [KebijakanController::class, 'get_kebijakan']);
    Route::get('get_berita_detail/{slug}', [BeritaController::class, 'get_berita_detail']);
    Route::get('get_tentang_uks_detail/{slug}', [TentangUKSController::class, 'get_tentang_uks_detail']);
    Route::get('get_program_detail/{slug}', [ProgramController::class, 'get_program_detail']);
    Route::get('getClient', [ClientController::class, 'get']);
    Route::get('getBerita', [BeritaController::class, 'getBerita']);
    Route::get('get_agenda/{page}', [AgendaController::class, 'get_agenda']);
    Route::get('get_agenda_detail/{agenda_id}', [AgendaController::class, 'get_agenda_detail']);
    Route::get('getfaq', [FAQController::class, 'getdata']);
    Route::get('getKategorifaq', [FAQController::class, 'getKategorifaq']);
    Route::get('getRekapWilayah', [AppController::class, 'getRekapWilayah']);
    Route::get('get_stratifikasi', [StratifikasiuksController::class, 'get_wilayah']);
    Route::get('get_stratifikasi_kab', [StratifikasiuksController::class, 'get_wilayah_kab']);
    Route::get('get_praktekbaik/{page}', [PraktekbaikController::class, 'get_praktekbaik']);
    Route::get('get_praktik_detail/{slug}', [PraktekbaikController::class, 'get_praktik_detail']);
    Route::get('get_aplikasi/{page}', [AplikasiController::class, 'get_aplikasi']);
    Route::get('get_aplikasi_detail/{aplikasi_id}', [AplikasiController::class, 'get_aplikasi_detail']);
    Route::get('get_publikasi/{page}', [PublikasiController::class, 'get_publikasi']);

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