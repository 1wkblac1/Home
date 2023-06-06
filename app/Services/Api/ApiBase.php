<?php

namespace App\Services\Api;



class ApiBase
{
    use ApiReturn;
    protected $result = [];

    protected function successResponse($message = '成功'){
        return $this->successResult($this->result,$message);
    }

}
