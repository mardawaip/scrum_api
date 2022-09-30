<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PeranController;
use App\Http\Controllers\JenisAgendaController;
use App\Http\Controllers\JenisBeritaController;
use App\Http\Controllers\BentukPendidikanController;
use App\Http\Controllers\JenisFaqController;
use App\Http\Controllers\KategoriBeritaController;
use App\Http\Controllers\JenisUnduhanController;
use App\Http\Controllers\JenisMenuController;
use App\Http\Controllers\KategoriProgramController;
use App\Http\Controllers\TypeInputController;
use App\Http\Controllers\JenisDokumenController;
use App\Http\Controllers\StatusBeritaController;
use App\Http\Controllers\KategoriArtikelController;
use App\Http\Controllers\JenisGaleriController;
use App\Http\Controllers\KategoriFaqController;
use App\Http\Controllers\TempatController;

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

Route::get('/users/{user}', [UserController::class, 'show']);

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

    Route::prefix('berita')->group(function(){
        Route::post('get', [BeritaController::class, 'get']);
        Route::post('add', [BeritaController::class, 'add']);
        Route::post('update', [BeritaController::class, 'update']);
        Route::post('delete', [BeritaController::class, 'delete']);
        Route::post('get_perberita', [BeritaController::class, 'get_perberita']);
        Route::post('upload', [BeritaController::class, 'upload']);
        Route::post('upload_images', [BeritaController::class, 'upload_images']);
        Route::get('getSelectBerita', [BeritaController::class, 'getSelectBerita']);
    });
    
    Route::prefix('agenda')->group(function(){
        Route::post('get', [AgendaController::class, 'get']);
        Route::post('add', [AgendaController::class, 'add']);
        Route::post('update', [AgendaController::class, 'update']);
        Route::post('delete', [AgendaController::class, 'delete']);
        Route::post('get_peragenda', [AgendaController::class, 'get_peragenda']);
        Route::post('upload', [AgendaController::class, 'upload']);
        Route::post('upload_images', [AgendaController::class, 'upload_images']);
        Route::get('getSelectAgenda', [AgendaController::class, 'getSelectAgenda']);
    });

    Route::prefix('tentang-uks')->group(function(){
        Route::post('get', [TentangUKSController::class, 'get']);
        Route::post('add', [TentangUKSController::class, 'add']);
        Route::post('update', [TentangUKSController::class, 'update']);
        Route::post('delete', [TentangUKSController::class, 'delete']);
        Route::post('get_pertentang_uks', [TentangUKSController::class, 'get_pertentang_uks']);
        Route::post('upload', [TentangUKSController::class, 'upload']);
        Route::post('upload_images', [TentangUKSController::class, 'upload_images']);
        Route::get('getSelectTentanguks', [TentangUKSController::class, 'getSelectTentanguks']);
    });

    Route::prefix('program')->group(function(){
        Route::post('get', [ProgramController::class, 'get']);
        Route::post('add', [ProgramController::class, 'add']);
        Route::post('update', [ProgramController::class, 'update']);
        Route::post('delete', [ProgramController::class, 'delete']);
        Route::post('get_perprogram', [ProgramController::class, 'get_perprogram']);
        Route::post('upload', [ProgramController::class, 'upload']);
        Route::post('upload_images', [ProgramController::class, 'upload_images']);
        Route::get('getSelect', [ProgramController::class, 'getSelect']);
    });

    Route::prefix('aplikasi')->group(function(){
        Route::post('get', [AplikasiController::class, 'get']);
        Route::post('add', [AplikasiController::class, 'add']);
        Route::post('update', [AplikasiController::class, 'update']);
        Route::post('delete', [AplikasiController::class, 'delete']);
        Route::post('get_perdata', [AplikasiController::class, 'get_perdata']);
        Route::post('upload', [AplikasiController::class, 'upload']);
        Route::post('upload_images', [AplikasiController::class, 'upload_images']);
        Route::get('getSelectTentanguks', [AplikasiController::class, 'getSelectTentanguks']);
        Route::post('saveFilesGambar', [AplikasiController::class, 'saveFilesGambar']);
        Route::post('getFilesperData', [AplikasiController::class, 'getFilesperData']);
        Route::post('deleteFile', [AplikasiController::class, 'deleteFile']);
    });

    Route::prefix('praktik_baik')->group(function(){
        Route::post('get', [PraktekbaikController::class, 'get']);
        Route::post('add', [PraktekbaikController::class, 'add']);
        Route::post('update', [PraktekbaikController::class, 'update']);
        Route::post('delete', [PraktekbaikController::class, 'delete']);
        Route::post('get_perdata', [PraktekbaikController::class, 'get_perdata']);
        Route::get('getSelect', [PraktekbaikController::class, 'getSelect']);
    });

    Route::prefix('kebijakan')->group(function(){
        Route::post('get', [KebijakanController::class, 'get']);
        Route::post('add', [KebijakanController::class, 'add']);
        Route::post('edit', [KebijakanController::class, 'edit']);
        Route::post('delete', [KebijakanController::class, 'delete']);
        Route::post('getperkebijakan', [KebijakanController::class, 'getperkebijakan']);
        Route::post('upload_images', [KebijakanController::class, 'upload_images']);
        Route::get('getSelect', [KebijakanController::class, 'getSelect']);
        Route::post('getfilesperdata', [KebijakanController::class, 'getfilesperdata']);
        Route::post('deleteFile', [KebijakanController::class, 'deleteFile']);
    });

    Route::prefix('produk_hukum')->group(function(){
        Route::post('get', [ProdukHukumController::class, 'get']);
        Route::post('add', [ProdukHukumController::class, 'add']);
        Route::post('edit', [ProdukHukumController::class, 'edit']);
        Route::post('delete', [ProdukHukumController::class, 'delete']);
        Route::post('getperprodukhukum', [ProdukHukumController::class, 'getperprodukhukum']);
        Route::post('upload_files', [ProdukHukumController::class, 'upload_files']);
        Route::get('getSelectProdukHukum', [ProdukHukumController::class, 'getSelectProdukHukum']);
        Route::post('getfilesperprodukhukum', [ProdukHukumController::class, 'getfilesperprodukhukum']);
        Route::post('deleteFile', [ProdukHukumController::class, 'deleteFile']);
    });

    Route::prefix('publikasi')->group(function(){
        Route::post('get', [PublikasiController::class, 'get']);
        Route::post('add', [PublikasiController::class, 'add']);
        Route::post('edit', [PublikasiController::class, 'edit']);
        Route::post('delete', [PublikasiController::class, 'delete']);
        Route::post('getperdata', [PublikasiController::class, 'getperdata']);
        Route::post('upload_files', [PublikasiController::class, 'upload_files']);
        Route::get('getSelect', [PublikasiController::class, 'getSelect']);
        Route::post('getfileperdata', [PublikasiController::class, 'getfileperdata']);
        Route::post('deleteFile', [PublikasiController::class, 'deleteFile']);
        Route::post('upload_cover', [PublikasiController::class, 'upload_cover']);
    });

    Route::prefix('faq')->group(function(){
        Route::post('get', [FAQController::class, 'get']);
        Route::post('add', [FAQController::class, 'add']);
        Route::post('edit', [FAQController::class, 'edit']);
        Route::post('delete', [FAQController::class, 'delete']);
        Route::post('getperfaq', [FAQController::class, 'getperfaq']);
        Route::get('getSelectfaq', [FAQController::class, 'getSelectfaq']);
    });

    Route::prefix('pengguna')->group(function(){
        Route::post('get', [PenggunaController::class, 'get']);
        Route::post('add', [PenggunaController::class, 'add']);
        Route::post('update', [PenggunaController::class, 'update']);
        Route::post('getPerpengguna', [PenggunaController::class, 'getPerpengguna']);
        Route::get("getSelectPengguna", [PenggunaController::class, 'getSelectPengguna']);
    });

    Route::prefix('menu')->group(function(){
        Route::post('get', [MenuController::class, 'GET_menu']);
        Route::post('add', [MenuController::class, 'simpan_permenu']);
        Route::post('update', [MenuController::class, 'update_permenu']);
        Route::post('delete', [MenuController::class, 'delete_permenu']);
        Route::post('nonaktif', [MenuController::class, 'nonaktif_permenu']);
        Route::post('getpermenu', [MenuController::class, 'GET_permenu']);
        Route::get('getSelectMenu', [MenuController::class, 'getSelectMenu']);
        Route::post('getmenuperan', [MenuController::class, 'GET_menu_peran']);
        Route::post('simpanMenuPeran', [MenuController::class, 'Simpan_menu_peran']);
    });

    Route::prefix('peran')->group(function(){
        Route::post('get', [PeranController::class, 'get']);
        Route::post('add', [PeranController::class, 'add']);
        Route::post('edit', [PeranController::class, 'edit']);
        Route::post('delete', [PeranController::class, 'delete']);
        Route::post('getperperan', [PeranController::class, 'getperperan']);
    });

    Route::prefix('jenisagenda')->group(function(){
        Route::post('get', [JenisAgendaController::class, 'get']);
        Route::post('add', [JenisAgendaController::class, 'add']);
        Route::post('edit', [JenisAgendaController::class, 'edit']);
        Route::post('delete', [JenisAgendaController::class, 'delete']);
        Route::post('getperjenisagenda', [JenisAgendaController::class, 'getperjenisagenda']);
    });
    
    Route::prefix('jenisberita')->group(function(){
        Route::post('get', [JenisBeritaController::class, 'get']);
        Route::post('add', [JenisBeritaController::class, 'add']);
        Route::post('edit', [JenisBeritaController::class, 'edit']);
        Route::post('delete', [JenisBeritaController::class, 'delete']);
        Route::post('getperjenisberita', [JenisBeritaController::class, 'getperjenisberita']);
    });
    
    Route::prefix('bentukpendidikan')->group(function(){
        Route::post('get', [BentukPendidikanController::class, 'get']);
        Route::post('add', [BentukPendidikanController::class, 'add']);
        Route::post('edit', [BentukPendidikanController::class, 'edit']);
        Route::post('delete', [BentukPendidikanController::class, 'delete']);
        Route::post('getperbentukpendidikan', [BentukPendidikanController::class, 'getperbentukpendidikan']);
    });
    
    Route::prefix('jenisfaq')->group(function(){
        Route::post('get', [JenisFaqController::class, 'get']);
        Route::post('add', [JenisFaqController::class, 'add']);
        Route::post('edit', [JenisFaqController::class, 'edit']);
        Route::post('delete', [JenisFaqController::class, 'delete']);
        Route::post('getperjenisfaq', [JenisFaqController::class, 'getperjenisfaq']);
    });

    Route::prefix('kategoriberita')->group(function(){
        Route::post('get', [KategoriBeritaController::class, 'get']);
        Route::post('add', [KategoriBeritaController::class, 'add']);
        Route::post('edit', [KategoriBeritaController::class, 'edit']);
        Route::post('delete', [KategoriBeritaController::class, 'delete']);
        Route::post('getperkategoriberita', [KategoriBeritaController::class, 'getperkategoriberita']);
    });

    Route::prefix('jenisunduhan')->group(function(){
        Route::post('get', [JenisUnduhanController::class, 'get']);
        Route::post('add', [JenisUnduhanController::class, 'add']);
        Route::post('edit', [JenisUnduhanController::class, 'edit']);
        Route::post('delete', [JenisUnduhanController::class, 'delete']);
        Route::post('getperjenisunduhan', [JenisUnduhanController::class, 'getperjenisunduhan']);
    });

    Route::prefix('client')->group(function(){
        Route::post('get', [ClientController::class, 'get']);
        Route::post('add', [ClientController::class, 'add']);
        Route::post('edit', [ClientController::class, 'edit']);
        Route::post('delete', [ClientController::class, 'delete']);
        Route::post('getperclient', [ClientController::class, 'getperclient']);
        Route::get('getSelectClient', [ClientController::class, 'getSelectClient']);
        Route::post('upload_images', [ClientController::class, 'upload_images']);
    });

    Route::prefix('manajemen-konten')->group(function(){
        Route::post('get_page', [ManajemenKontenController::class, 'get_page']);
        Route::post('tableSimpan', [ManajemenKontenController::class, 'tableSimpan']);
    });

    Route::prefix('jenismenu')->group(function(){
        Route::post('get', [JenisMenuController::class, 'get']);
        Route::post('add', [JenisMenuController::class, 'add']);
        Route::post('edit', [JenisMenuController::class, 'edit']);
        Route::post('delete', [JenisMenuController::class, 'delete']);
        Route::post('getperjenismenu', [JenisMenuController::class, 'getperjenismenu']);
    });

    Route::prefix('kategoriprogram')->group(function(){
        Route::post('get', [KategoriProgramController::class, 'get']);
        Route::post('add', [KategoriProgramController::class, 'add']);
        Route::post('edit', [KategoriProgramController::class, 'edit']);
        Route::post('delete', [KategoriProgramController::class, 'delete']);
        Route::post('getperkategoriprogram', [KategoriProgramController::class, 'getperkategoriprogram']);
    });

    Route::prefix('typeinput')->group(function(){
        Route::post('get', [TypeInputController::class, 'get']);
        Route::post('add', [TypeInputController::class, 'add']);
        Route::post('edit', [TypeInputController::class, 'edit']);
        Route::post('delete', [TypeInputController::class, 'delete']);
        Route::post('getpertypeinput', [TypeInputController::class, 'getpertypeinput']);
    });
    
    Route::prefix('jenisdokumen')->group(function(){
        Route::post('get', [JenisDokumenController::class, 'get']);
        Route::post('add', [JenisDokumenController::class, 'add']);
        Route::post('edit', [JenisDokumenController::class, 'edit']);
        Route::post('delete', [JenisDokumenController::class, 'delete']);
        Route::post('getperjenisdokumen', [JenisDokumenController::class, 'getperjenisdokumen']);
    });

    Route::prefix('statusberita')->group(function(){
        Route::post('get', [StatusBeritaController::class, 'get']);
        Route::post('add', [StatusBeritaController::class, 'add']);
        Route::post('edit', [StatusBeritaController::class, 'edit']);
        Route::post('delete', [StatusBeritaController::class, 'delete']);
        Route::post('getperstatusberita', [StatusBeritaController::class, 'getperstatusberita']);
    });

    Route::prefix('kategoriartikel')->group(function(){
        Route::post('get', [KategoriArtikelController::class, 'get']);
        Route::post('add', [KategoriArtikelController::class, 'add']);
        Route::post('edit', [KategoriArtikelController::class, 'edit']);
        Route::post('delete', [KategoriArtikelController::class, 'delete']);
        Route::post('getperkategoriartikel', [KategoriArtikelController::class, 'getperkategoriartikel']);
    });

    Route::prefix('jenisgaleri')->group(function(){
        Route::post('get', [JenisGaleriController::class, 'get']);
        Route::post('add', [JenisGaleriController::class, 'add']);
        Route::post('edit', [JenisGaleriController::class, 'edit']);
        Route::post('delete', [JenisGaleriController::class, 'delete']);
        Route::post('getperjenisgaleri', [JenisGaleriController::class, 'getperjenisgaleri']);
    });

    Route::prefix('kategorifaq')->group(function(){
        Route::post('get', [KategoriFaqController::class, 'get']);
        Route::post('add', [KategoriFaqController::class, 'add']);
        Route::post('edit', [KategoriFaqController::class, 'edit']);
        Route::post('delete', [KategoriFaqController::class, 'delete']);
        Route::post('getperkategorifaq', [KategoriFaqController::class, 'getperkategorifaq']);
    });

    Route::prefix('tempat')->group(function(){
        Route::post('get', [TempatController::class, 'get']);
        Route::post('add', [TempatController::class, 'add']);
        Route::post('edit', [TempatController::class, 'edit']);
        Route::post('delete', [TempatController::class, 'delete']);
        Route::post('getpertempat', [TempatController::class, 'getpertempat']);
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