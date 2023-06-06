<?php


namespace App\Exceptions;


class ParamsException extends \Exception
{
    protected $data;

    public function __construct($message = '参数错误', $code = ERROR_PARAMS, $data = null)
    {
        parent::__construct($message, $code, $data);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
