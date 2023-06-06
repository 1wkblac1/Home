<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Home\LoginController;
use App\Http\Controllers\Api\Home\AdminController;
use App\Http\Controllers\Api\Home\ColumnController;
use App\Http\Controllers\Api\Home\CameraController;
use App\Http\Controllers\Api\FileController;

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

/*
 * @登录模块
 */

Route::controller(LoginController::class)->middleware('write_log')->group(function () {
    Route::get('/login', 'login'); // 登录
});

/*
 * @管理员模块
 */

Route::controller(AdminController::class)->middleware('home')->group(function () {
    Route::get('/permissions', 'permissions'); // 个人权限及信息
});

/*
 * @栏目模块
 */

Route::controller(ColumnController::class)->middleware('home')->group(function () {
    Route::get('/columns', 'columns'); // 列表
    Route::post('/columnAdd', 'columnAdd'); // 新增
    Route::put('/columnModify', 'columnModify'); // 修改
    Route::get('/columnDetail', 'columnDetail'); // 详情
    Route::delete('/columnDelete', 'columnDelete'); // 删除
});

/*
 * @动态模块
 */

Route::controller(CameraController::class)->middleware('home')->group(function () {
    Route::get('/cameras', 'cameras'); // 列表
    Route::post('/cameraAdd', 'cameraAdd'); // 新增
    Route::put('/cameraModify', 'cameraModify'); // 修改
    Route::put('/cameraListModify', 'cameraListModify'); // 列表更新
    Route::get('/cameraDetail', 'cameraDetail'); // 详情
    Route::delete('/cameraDelete', 'cameraDelete'); // 删除
});


Route::controller(FileController::class)->group(function () {
    Route::post('/uploadFile', 'uploadFile'); //
});
