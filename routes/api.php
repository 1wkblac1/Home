<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
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

// 测试
Route::controller(TestController::class)->group(function () {
    Route::get('/test', 'test'); //
});

// 文件上传
Route::controller(FileController::class)->group(function () {
    Route::post('/uploadFile', 'uploadFile'); //
});

