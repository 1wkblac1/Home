<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\Api\File\UpLoad;

class FileController extends Controller
{
    /*
     * 文件上传
     */
    public function uploadFile() {
        return api(UpLoad::class);
    }

}
