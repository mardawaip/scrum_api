<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Logs\SystemLogsController;
use App\Http\Controllers\RolesPermissionController;
use App\Http\Controllers\Logs\AuditLogsController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MipCaptchaController;
use App\Http\Controllers\SampleDataController;
use App\Http\Controllers\RolesMenuController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;
use App\Models\ActivityLog;
use App\Models\Menu;

use App\Http\Controllers\ScrumController;


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
// Route::get('/users', [SampleDataController::class, 'getUsers']);

// MENU
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/user_info', [UserInfoController::class, 'index']);
Route::get('/roles', [RolesController::class, 'index']);
Route::get('/roles_menu', [RolesMenuController::class, 'index']);
Route::get('/permission', [PermissionController::class, 'index']);
Route::get('/countries', [UsersController::class, 'countries']);

Route::get('captcha/{captcha_token}.jpg', [MipCaptchaController::class, 'getCaptchaImage']);
Route::get('captcha/get', [MipCaptchaController::class, 'getCaptcha']);
Route::get('captcha/check', [MipCaptchaController::class, 'checkCaptcha']);
Route::post('getCaptcha', [MipCaptchaController::class, 'getCaptcha']);
Route::post('getCaptcha', [MipCaptchaController::class, 'getCaptcha']);

Route::group(['middleware' => ['tokenaccess']], function() {
    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::post('/users/{id}', [UsersController::class, 'getPer']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
    Route::post('upload', [KontenController::class, 'upload']);
    
    Route::get('/roles_permission', [RolesPermissionController::class, 'index']);
    Route::post('/roles_permission', [RolesPermissionController::class, 'store']);
    Route::delete('/roles_permission/{permission_id}/{role_id}', [RolesPermissionController::class, 'destroy']);

    Route::prefix('dashboards')->group(function(){
        Route::get('widgets', [PagesController::class, 'widgets']);
    });

    Route::prefix('scrum')->group(function(){
        Route::post('getMembers', [ScrumController::class, 'getMembers']);
        Route::post('getScrum', [ScrumController::class, 'getScrum']);
        Route::get('getScrum/{id}', [ScrumController::class, 'getScrumDetail']);
        Route::post('addScrum', [ScrumController::class, 'addScrum']);
        Route::put('updateScrum/{id}', [ScrumController::class, 'updateScrum']);
        Route::get('getAplikasiDetail/{id}', [ScrumController::class, 'getAplikasiDetail']);
        Route::post('tasks', [ScrumController::class, 'tasks_add']);
        Route::put('tasks/{id}', [ScrumController::class, 'tasks_update']);
        Route::delete('tasks/{id}', [ScrumController::class, 'tasks_delete']);
        Route::post('tasks/reorderList', [ScrumController::class, 'reorderList']);
        Route::post('newCard', [ScrumController::class, 'newCard']);
        Route::post('updateCard', [ScrumController::class, 'updateCard']);
        Route::post('reorderCard', [ScrumController::class, 'reorderCard']);
        Route::post('reorderListCard', [ScrumController::class, 'reorderListCard']);
    });

    Route::prefix('profil')->group(function(){
        Route::get('get_profil', [ProfilController::class, 'index']);
        Route::get('get_log', [ProfilController::class, 'get_log']);
    });

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

    Route::post('/auth/access-token', [AuthenticatedSessionController::class, 'accessToken']);    
    // Logs pages
    Route::prefix('log')->name('logApi')->group(function () {
});

});

Route::get('system', [ActivityLogController::class,'index']);
Route::post('system', [ActivityLogController::class,'index']);
