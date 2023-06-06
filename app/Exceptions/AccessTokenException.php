<?php


namespace App\Exceptions;


class AccessTokenException extends \Exception
{
    protected $data;

    public function __construct($code = ERROR_PARAMS, $message = '请先登录', $data = null)
    {
        parent::__construct($message, $code, $data);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
