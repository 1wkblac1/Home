<?php

namespace App\Services\Api;

use App\Exceptions\AccessTokenException;
use App\Exceptions\HandleException;
use App\Exceptions\NoTargetException;
use App\Exceptions\ParamsException;

trait ApiReturn
{
    public function successResult(array $result, String $message = '成功')
    {
        return $this->result(SUCCESS, $message, $result);
    }

    /*
     * 目标不存在
     */
    public function noTargetErrorResult($e, $code = ERROR_NO_TARGET, $message = '目标不存在', $result = null)
    {
        if ($e instanceof NoTargetException) {
            return $this->result($e->getCode(), $e->getMessage());
        } else {
            return $this->result($code, $message, $result);
        }
    }

    /*
     * token错误
     */
    public function accessTokenErrorResult($e, $code = TOKEN_NULL, $message = '请先登录', $result = null)
    {
        if ($e instanceof AccessTokenException) {
            return $this->result($e->getCode(), $e->getMessage());
        } else {
            return $this->result($code, $message, $result);
        }
    }

    /*
     * 处理异常
     */
    public function handleErrorResult($e, $code = ERROR_PARAMS, $message = '执行失败', $result = null)
    {
        if ($e instanceof HandleException) {
            return $this->result($e->getCode(), $e->getMessage());
        } else {
            return $this->result($code, $message, $result);
        }
    }

    /*
     * 参数异常
     */
    public function paramsErrorResult($e, $code = ERROR_PARAMS, $message = '参数错误', $result = null)
    {
        if ($e instanceof ParamsException) {
            return $this->result($e->getCode(), $e->getMessage());
        } else {
            return $this->result($code, $message, $result);
        }
    }

    private function result($code, $message, $result = null)
    {
        $returnData = [];
        $returnData['code'] = $code;
        $returnData['message'] = $message;
        if (empty($result)) {
            $returnData['result'] = new \stdClass();
        } else {
            $returnData['result'] = $result;
        }
        return $returnData;
    }
}
