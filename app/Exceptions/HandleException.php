<?php


namespace App\Exceptions;


class HandleException extends \Exception
{
    protected $data;

    public function __construct($message = '处理错误', $code = ERROR_HANDLE,  $data = null)
    {
        parent::__construct($message, $code, $data);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
