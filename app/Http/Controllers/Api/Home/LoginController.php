<?php


namespace App\Http\Controllers\Api\Home;


use App\Http\Controllers\Controller;
use App\Services\Api\Home\Login\Login;

class LoginController extends Controller
{
    /*
     * @ 登录接口
     */
    public function login() {
        return api(Login::class);
    }
}
