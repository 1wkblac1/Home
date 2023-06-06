<?php


namespace App\Utils;


use App\Models\AccessToken;
use App\Models\Admin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class UserCache
{
    public static $expire = 60;
    public static $expire_user = 10;

    /**
     * 验证access_token并保存session
     * @param string $access_token
     * @return bool
     */
    public static function verifyAccessToken(int $target_type = 1,string $access_token = ""){
        $value = Cache::remember("access_token{$target_type}{$access_token}", self::$expire, function () use ($target_type,$access_token) {
            $where['target_type'] = $target_type;
            $where['access_token'] = $access_token;
            $where[] = ['expire_time','>',time()];
            AccessToken::query();
            $accessTokenModel = AccessToken::query();
            $accessTokenInfo = $accessTokenModel->where($where)->first();
            if(!empty($accessTokenInfo)){
                $accessTokenInfo->expire_time = time() + LOGIN_EXPIRE_TIME;
                $accessTokenInfo->save();
            }
            return $accessTokenInfo;
        });
        if(!$value){
            return false;
        }
        else{
            $data['target_id'] = $value->target_id;
            $data['target_type'] = $value->target_type;
            Session::put($data);
            return true;
        }
    }

    /*
     * 获取当前登录者的信息
     */
    public static function getUserField($field='id') {
        $target_id = Session::get('target_id');
        $target_type = Session::get('target_type');
        $first = Cache::remember("user_filed{$target_type}{$target_id}", self::$expire_user, function () use ($target_id,$target_type) {
            switch ($target_type) {
                case LOGIN_USER_TYPE_HOME:
                    $adminModel = Admin::query();
                    $adminModel->where('id',$target_id);
                    return $adminModel->first();
            }
        });
        return isset($first[$field]) ? $first[$field] : '';
    }
}
