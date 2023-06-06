<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $headers = [
//            'Content-Type'=> 'text/html;charset=utf-8',
//            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
//            'Access-Control-Allow-Headers'=> 'Accept,access_token,content-type,home-pathContent-Type,Fetch-Mode,accept,*',
//            'Access-Control-Allow-Credentials'=> 'true',
//            'Cache-control'=> 'no-cache,no-store,must-revalidate',
//            'Pragma'=> 'no-cache',
//            'Expires'=> '0',
//        ];
        return $next($request)
            ->header('Access-Control-Allow-Origin','*')
            ->header('Access-Control-Allow-Methods','POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Credentials','true')
            ->header('Access-Control-Allow-Headers','*');

    }
}
