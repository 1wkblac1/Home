<?php
use App\Exceptions\ParamsException;
/*
 * 调用服务
 */

function api($apiInterfaceImplementClass){
    $api = app()->make($apiInterfaceImplementClass);
    $api->validate();
    $api->handle();
    return $api->response();
}

/*
 * 参数验证
 */

function validate_input_or_exception(array $data, array $rules, array $messages = [], array $attributes = [])
{
    //排除api_token
    if (isset($data['api_token'])) {
        unset($data['api_token']);
    }
    if (count(array_diff(array_keys($data), array_keys($rules)))) {
        $array_diff = array_diff(array_keys($data), array_keys($rules));

        foreach ($array_diff as $key => $val){
            if(!array_key_exists($val.".*",$rules) && !array_key_exists($val.".*.*",$rules)){
                throw new ParamsException( '存在未知参数'.$val);
            }
        }
    }
    $validator = Illuminate\Support\Facades\Validator::make($data, $rules, $messages, $attributes);
    if ($validator->fails()) {
        throw new ParamsException($validator->messages()->first());
    }
}

/**
 * 根据指定的方式获取参数，并删除参数中指定的字段
 * @param string $pattern
 * @param array $unParams
 * @return array|mixed|string
 */

function getRequest(string $pattern = 'input',string $value = '',array $unParams = ['api_token']){
    switch ($pattern){
        case 'input':
            if(empty($value)){
                $param = request()->input();
            }
            else{
                $param = request()->input($value);
            }
            break;
        case 'post':
            if(empty($value)){
                $param = request()->post();
            }
            else{
                $param = request()->post($value);
            }
            break;
        case 'get':
            $param = request()->get($value,'');
            break;
        case 'all':
            if(empty($value)){
                $param = request()->all();
            }
            else{
                $param = request()->all($value);
            }
            break;
        case 'route':
        case 'routes':
            $param = request()->route($value);
            break;
        default:
            $param = [];
    }
    if($pattern == 'routes'){
        if (is_string($param)){
            $startSymbol = substr($param,0,1);
            $endSymbol = substr($param,strlen($param)-1,strlen($param));
            if($startSymbol == "[" && $endSymbol == "]"){
                $param = preg_replace("/[\[\]]/","",$param);
            }
            return explode(",",rtrim($param,","));
        }
    }
    if($pattern == 'route'){
        return $param;
    }
    if(is_array($param) && count($unParams)){
        foreach ($unParams as $unParam){
            unset($param[$unParam]);
        }
    }
    return $param;
}

function getOssBindDomain($path,$state = 1){
    $protocol = "http://";
    $domain = env('OSS_BUCKET').".".env("OSS_ENDPOINT");
    if(env('OSS_SSL')){
        $protocol = "https://";
    }
    if(env('OSS_ISCNAME')){
        $domain = env('OSS_CDNDOMAIN');
    }
    $url = $protocol.$domain."/";
    if($state == 2){
        return $url;
    }
    if(!empty($path)){
        $tempPath = parse_url($path);
        if(isset($tempPath['scheme'])){
            return $path;
        }
        return $url.$path;
    }
    return '';
}

/**
 * 度分秒转换经纬度
 * @param string $string 度分秒字符串
 */
function latToLat($string) {
    $arr = explode(',' ,$string);
    $res = $arr[0]+$arr[1]/60+$arr[2]/3600;
    return sprintf("%.6f",$res);
}

/**
 * 过滤特殊字符串
 * @param string $string 要过滤的字符串
 * @param string $string 要过滤的特殊字符串
 */
function filtrationStr($str, $symbol){
    $str = str_replace($symbol, '', $str);
    return trim($str);
}
