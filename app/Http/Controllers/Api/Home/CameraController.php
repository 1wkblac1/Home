<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Services\Api\Home\Camera;

class CameraController extends Controller
{
    // 列表
    public function cameras() {
        return api(Camera\Cameras::class);
    }

    // 新增
    public function cameraAdd() {
        return api(Camera\CameraAdd::class);
    }

    // 修改
    public function cameraModify() {
        return api(Camera\CameraModify::class);
    }

    // 列表修改
    public function cameraListModify() {
        return api(Camera\CameraListModify::class);
    }

    // 详情
    public function cameraDetail() {
        return api(Camera\CameraDetail::class);
    }

    // 删除
    public function cameraDelete() {
        return api(Camera\CameraDelete::class);
    }
}
