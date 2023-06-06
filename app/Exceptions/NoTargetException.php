<?php


namespace App\Exceptions;


class NoTargetException extends \Exception
{
    protected $data;

    public function __construct($code = ERROR_PARAMS, $message = '目标不存在', $data = null)
    {
        parent::__construct($message, $code, $data);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
