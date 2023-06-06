<?php

namespace App\Http\Middleware;

use App\Exceptions\AccessTokenException;
use App\Utils\UserCache;
use Closure;

class VerifyLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $authorization = $request->header('authorization');
        // 验证请求头
        if (!$authorization) throw new AccessTokenException(TOKEN_EXPIRE, '请先登录');
        list($type, $access_token) = explode(' ', $authorization);
        // 验证请求头类型和token
        if ($type !== 'Bearer') throw new AccessTokenException(TOKEN_EXPIRE, '请先登录');
        $target_type = $request->header("TargetType");
        if(empty($access_token)) throw new AccessTokenException(TOKEN_EXPIRE, '请先登录');
        // 验证token是否过期
        if(!UserCache::verifyAccessToken($target_type,$access_token)) throw new AccessTokenException(TOKEN_EXPIRE, '登录过期，请重新登录');
        return $next($request);
    }
}
