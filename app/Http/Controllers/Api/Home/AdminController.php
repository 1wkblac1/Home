<?php


namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Services\Api\Home\Permission\Permissions;

class AdminController extends Controller
{
    /*
     * @ 个人信息及权限接口
     */
    public function permissions() {
        return api(Permissions::class);
    }
}
