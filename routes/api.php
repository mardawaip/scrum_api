<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Logs\SystemLogsController;
use App\Http\Controllers\RolesPermissionController;
use App\Http\Controllers\Logs\AuditLogsController;
use App\Http\Controllers\MgnRagamDataController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MipCaptchaController;
use App\Http\Controllers\SampleDataController;
use App\Http\Controllers\RagamDataController;
use App\Http\Controllers\RolesMenuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\MgnMenuController;
use App\Http\Controllers\GeoJsonController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;
use App\Models\ActivityLog;
use App\Models\Menu;


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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Sample API route
Route::get('/profits', [SampleDataController::class, 'profits'])->name('profits');

Route::post('/register', [RegisteredUserController::class, 'apiStore']);

Route::post('/login', [AuthenticatedSessionController::class, 'apiStore']);

Route::post('/forgot_password', [PasswordResetLinkController::class, 'apiStore']);

Route::post('/verify_token', [AuthenticatedSessionController::class, 'apiVerifyToken']);

Route::get('/users', [SampleDataController::class, 'getUsers']);

Route::post('getGeoJsonBasic', [GeoJsonController::class, 'getGeoJsonBasic']);

Route::get('setvisitor', [VisitorController::class, 'setvisitor']);
Route::get('getVisitor', [VisitorController::class, 'getVisitor']);

Route::get('/grafiktime', [RagamDataController::class, 'getGrafikTime']);

// MENU
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/user_info', [UserInfoController::class, 'index']);
Route::get('/roles', [RolesController::class, 'index']);
Route::get('/roles_menu', [RolesMenuController::class, 'index']);
Route::get('/permission', [PermissionController::class, 'index']);
Route::get('/users', [UsersController::class, 'index']);
Route::get('/countries', [UsersController::class, 'countries']);

// PORTAL
Route::prefix('portal')->group(function(){
    Route::post('getMenu', [MenuController::class, 'portal']);
    Route::post('getAlbum0', [GaleriController::class, 'getAlbum0']);
    Route::post('getGaleri', [GaleriController::class, 'getGaleri']);
    Route::post('halaman', [KontenController::class, 'halaman']);
    Route::post('getPost', [KontenController::class, 'get_']);
    Route::post('getperPost', [KontenController::class, 'get_per']);
    Route::get('getBanner', [BannerController::class, 'getBanner']);
    Route::get('get_izin_lingkungan', [KontenController::class, 'get_izin_lingkungan']);
    Route::post('getLoadData/{table}', [KontenController::class, 'getLoadData']);
});

Route::get('captcha/{captcha_token}.jpg', [MipCaptchaController::class, 'getCaptchaImage']);
Route::get('captcha/get', [MipCaptchaController::class, 'getCaptcha']);
Route::get('captcha/check', [MipCaptchaController::class, 'checkCaptcha']);
Route::post('getCaptcha', [MipCaptchaController::class, 'getCaptcha']);
Route::post('getCaptcha', [MipCaptchaController::class, 'getCaptcha']);

Route::group(['middleware' => ['tokenaccess']], function() {
    Route::post('/users', [UsersController::class, 'store']);
    Route::post('/users/{id}', [UsersController::class, 'getPer']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/destroy/{id}', [UsersController::class, 'destroy']);
    Route::post('upload', [KontenController::class, 'upload']);
    
    Route::get('/roles_permission', [RolesPermissionController::class, 'index']);
    Route::post('/roles_permission', [RolesPermissionController::class, 'store']);
    Route::delete('/roles_permission/{permission_id}/{role_id}', [RolesPermissionController::class, 'destroy']);

    Route::prefix('menu')->group(function(){
        Route::post('get', [MgnMenuController::class, 'GET_menu']);
        Route::post('add', [MgnMenuController::class, 'simpan_permenu']);
        Route::post('update', [MgnMenuController::class, 'update_permenu']);
        Route::post('delete', [MgnMenuController::class, 'delete_permenu']);
        Route::post('nonaktif', [MgnMenuController::class, 'nonaktif_permenu']);
        Route::post('getpermenu', [MgnMenuController::class, 'GET_permenu']);
        Route::get('getSelectMenu', [MgnMenuController::class, 'getSelectMenu']);
        Route::post('getmenuperan', [MgnMenuController::class, 'GET_menu_peran']);
        Route::post('simpanMenuPeran', [MgnMenuController::class, 'Simpan_menu_peran']);
    });

    Route::prefix('konten')->group(function(){
        Route::post('halaman', [KontenController::class, 'halaman']);
        Route::get('getSelectKonten', [KontenController::class, 'getSelectKonten']);
        Route::post('get', [KontenController::class, 'get']);
        Route::post('per_get', [KontenController::class, 'per_get']);
        Route::post('add', [KontenController::class, 'add']);
        Route::post('update', [KontenController::class, 'update']);
        Route::post('delete', [KontenController::class, 'delete']);
        Route::post('upload', [KontenController::class, 'upload']);
        Route::post('upload_images', [KontenController::class, 'upload_images']);
    });

    Route::prefix('kategori')->group(function(){
        Route::post('get', [KategoriController::class, 'get']);
        Route::get('getSelectKategori', [KategoriController::class, 'getSelectKategori']);
        Route::post('getperkategori', [KategoriController::class, 'getperkategori']);
        Route::post('add', [KategoriController::class, 'add']);
        Route::post('update', [KategoriController::class, 'update']);
        Route::post('delete', [KategoriController::class, 'delete']);
    });

    Route::prefix('banner')->group(function(){
        Route::get('get', [BannerController::class, 'index']);
        Route::post('show', [BannerController::class, 'show']);
        Route::post('store', [BannerController::class, 'store']);
        Route::put('update/{id}', [BannerController::class, 'update']);
        Route::delete('destroy/{id}', [BannerController::class, 'destroy']);
        Route::post('upload', [BannerController::class, 'upload']);
    });

    Route::prefix('galeri')->group(function(){
        Route::post('get', [GaleriController::class, 'get']);
        Route::post('store_album', [GaleriController::class, 'store_album']);
        Route::delete('delete_album/{id}', [GaleriController::class, 'delete_album']);
        Route::put('update_album/{id}', [GaleriController::class, 'update_album']);
        Route::post('store_galeri', [GaleriController::class, 'store_galeri']);
        Route::delete('delete_galeri/{id}', [GaleriController::class, 'delete_galeri']);
        Route::put('update_galeri/{id}', [GaleriController::class, 'update_galeri']);
        Route::post('upload', [GaleriController::class, 'upload']);
    });

    Route::prefix('statistik')->group(function(){
        Route::post('getWidgets', [VisitorController::class, 'getWidgets']);
    });

    Route::get('mgnRagamData/{module}/{id}', [MgnRagamDataController::class, 'getPerdata']);
    Route::post('mgnRagamData/{module}', [MgnRagamDataController::class, 'index']);
    Route::get('ragamData/widgets/{$module}', [RagamDataController::class, 'widgets']);
    Route::get('ragamData/widgets', [RagamDataController::class, 'widgets']);
});

Route::group(['middleware' => ['tokenaccess']], function () {
    Route::get('/users/{id}', [UsersController::class, 'show']);

    Route::post('/menu', [MenuController::class, 'store']);
    Route::get('/menu/{id}', [MenuController::class, 'show']);
    Route::put('/menu/{id}', [MenuController::class, 'update']);
    Route::delete('/menu/{id}', [MenuController::class, 'destroy']);

    Route::post('/roles', [RolesController::class, 'store']);
    Route::get('/roles/{id}', [RolesController::class, 'show']);
    Route::put('/roles/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/{id}', [RolesController::class, 'destroy']);

    Route::post('/roles_menu', [RolesMenuController::class, 'store']);
    Route::get('/roles_menu/{id}', [RolesMenuController::class, 'show']);
    Route::put('/roles_menu/{id}', [RolesMenuController::class, 'update']);
    Route::delete('/roles_menu/{id}', [RolesMenuController::class, 'destroy']);
    
    Route::post('/permission', [PermissionController::class, 'store']);
    Route::get('/permission/{id}', [PermissionController::class, 'show']);
    Route::put('/permission/{id}', [PermissionController::class, 'update']);
    Route::delete('/permission/{id}', [PermissionController::class, 'destroy']);
    
    Route::post('/user_info', [UserInfoController::class, 'store']);
    Route::get('/user_info/{id}', [UserInfoController::class, 'show']);
    Route::put('/user_info/{id}', [UserInfoController::class, 'update'])->name('update-userinfo');
    Route::delete('/user_info/{id}', [UserInfoController::class, 'destroy']);

    Route::get('/auth/access-token', [AuthenticatedSessionController::class, 'accessToken']);    
    // Logs pages
    Route::prefix('log')->name('logApi')->group(function () {
    // Route::resource('system', ActivityLogController::class)->only(['index', 'destroy']);
    // Route::resource('audit', AuditLogsController::class)->only(['index', 'destroy']);
});

});

Route::get('system', [ActivityLogController::class,'index']);
Route::post('system', [ActivityLogController::class,'index']);

Route::get('ragamData/widgets', [RagamDataController::class, 'widgets']);
Route::get('ragamData/{type}/{tahun}/{lokasi}/{sungai}', [RagamDataController::class, 'index']);
Route::get('ragamData/{type}/{tahun}/{lokasi}', [RagamDataController::class, 'index']);
Route::get('ragamData/widgets/{module}', [RagamDataController::class, 'widgets']);
Route::get('ragamData/widgets', [RagamDataController::class, 'widgets']);
