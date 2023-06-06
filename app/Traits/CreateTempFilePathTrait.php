<?php


namespace App\Traits;


trait CreateTempFilePathTrait
{
    private $fileType;//文件类型名称
    private $purposeName = "public";
    public  $dirOption = [
        1 => 'images',
        2 => 'videos',
        3 => 'radios',
        4 => 'files',
    ];
    //根据类型生产目录路径
    public function getPath(){
        $date = date("Ymd");
        return "{$this->dirOption[$this->fileType]}/{$this->purposeName}/$date/";
    }
}