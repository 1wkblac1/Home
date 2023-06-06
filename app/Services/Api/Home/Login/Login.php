<?php


namespace App\Services\Api\Home\Login;


use App\Exceptions\HandleException;
use App\Models\AccessToken;
use App\Models\Admin;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;
use Illuminate\Support\Str;

class Login extends ApiBase implements ApiInterface
{
    private $Admin;
    private $AccessToken;
    private $info;
    private $data;
    private $rules = [
        'username'  => 'required',
        'password'  => 'required',
    ];
    public function __construct(Admin $admin, AccessToken $AccessToken)
    {
        $this->Admin = $admin->newQuery();
        $this->AccessToken = $AccessToken->newQuery();
        $this->data = getRequest();
    }

    public function validate()
    {
        // 参数验证
        validate_input_or_exception($this->data,$this->rules);
        // 账户验证
        $info = $this->Admin->firstWhere('username',$this->data['username']);
        if (empty($info)) {
            throw new HandleException( '无该账户信息');
        }
        if (!password_verify($this->data['password'], $info->password)) {
            throw new HandleException( '密码错误');
        }
        $this->info = $info;
    }

    public function handle()
    {
        $where['target_type'] = USER_TYPE_ADMIN;
        $where['target_id'] = $this->info->id;
        $where[] = ['expire_time', '>', time()];
        $access_token = $this->AccessToken->where($where)->value('access_token');
        if ($access_token) {
            $data['access_token'] = $access_token;
        } else {
            unset($where[0]);
            $access_token_info = $this->AccessToken->where($where)->first();
            if (empty($access_token_info)) {
                // 新增
                $this->AccessToken = new AccessToken();
                $this->AccessToken->access_token = Str::random(60);
                $this->AccessToken->target_type = USER_TYPE_ADMIN;
                $this->AccessToken->target_id = $this->info->id;
                $this->AccessToken->ip = request()->getClientIp();
                $this->AccessToken->expire_time = time() + LOGIN_EXPIRE_TIME;
                if (!$this->AccessToken->save()) {
                    throw new HandleException( '登录失败（CODE:0001）');
                }
                $data['access_token'] = $this->AccessToken->access_token;
            } else {
                // 修改
                $access_token_info->access_token = Str::random(60);
                $access_token_info->ip = request()->getClientIp();
                $access_token_info->expire_time = time() + LOGIN_EXPIRE_TIME;
                if (!$access_token_info->save()) {
                    throw new HandleException('登录失败（CODE:0002）');
                }
                $data['access_token'] = $access_token_info->access_token;
            }
        }

        $this->result = $data;
    }

    public function response(): array
    {
        return $this->successResponse('登录成功');
    }
}
