<?php


namespace App\Services\Api\File;


use App\Exceptions\HandleException;
use App\Models\TempFile;
use App\Traits\CreateTempFilePathTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use OSS\OssClient;

class UploadFileServer
{
    use CreateTempFilePathTrait;
    private $fileType;//文件类型
    private $purposeName = "public";//用途名称
    private $uploadDirPath;//上传目录路径
    private $fileSize;//文件大小
    private $suffixName;//文件拓展名
    private $originalFilename;//原文件名
    private $filePath;//文件路径
    private $width;//宽
    private $height;//高
    private $fileName;//文件名
    private $tempDirPath;//临时目录路径
    private $accessKeyId;
    private $accessKeySecret;
    private $endpoint;
    private $bucket;
    private $ossConnect;
    /**
     * 允许的图片后缀
     *
     * @var array
     */
    private static $allowed_image_extensions = [
        "png",
        "jpg",
        "gif",
        "PNG",
        "JPG",
        "GIF",
        "jpeg",
        "JPEG"
    ];

    /**
     * 允许的音频后缀
     *
     * @var array
     */
    private static $allowed_video_extensions = [
        "mp4",
        "MP4",
        "MOV",
        "mov",
    ];

    /**
     * 允许的音频后缀
     *
     * @var array
     */
    private static $allowed_radio_extensions = [
        'mp3',
        'wma',
        'wav',
        'MP3',
        'WMA',
        'WAV',
        'amr'
    ];

    /**
     * 允许的文件后缀
     *
     * @var array
     */
    private static $allowed_file_extensions = [
        "rar",
        "tar",
        "7z",
        "gz",
        "zip",
        "bz2",
        "xz",
        "z",
        "RAR",
        "TAR",
        "7Z",
        "GZ",
        "ZIP",
        "BZ2",
        "XZ",
        "Z"
    ];


    public function __construct(array $config = [])
    {
        ignore_user_abort(true);
        ini_set('memory_limit', '10240M');
        set_time_limit(0);
        $this->accessKeyId = env('OSS_ACCESS_ID');
        $this->accessKeySecret = env('OSS_ACCESS_KEY');
        $this->endpoint =  env('OSS_ENDPOINT_INTERNAL') ? env('OSS_ENDPOINT_INTERNAL') : env('OSS_ENDPOINT');
        $this->bucket = env('OSS_BUCKET');
        if(count($config)){
            $this->accessKeyId = isset($config['OSS_ACCESS_ID']) ? isset($config['OSS_ACCESS_ID']) : $this->accessKeyId;
            $this->accessKeySecret = isset($config['OSS_ACCESS_KEY']) ? isset($config['OSS_ACCESS_KEY']) : $this->accessKeySecret;
            $this->endpoint = isset($config['OSS_ENDPOINT']) ? isset($config['OSS_ENDPOINT']) : $this->endpoint;
            $this->bucket = isset($config['OSS_BUCKET']) ? isset($config['OSS_BUCKET']) : $this->bucket;
        }
        $this->ossConnect = new OssClient($this->accessKeyId,$this->accessKeySecret,$this->endpoint);
    }

    /**
     * 复制对象
     * @param $fromObject
     * @param $toObject
     * @return bool
     * @throws \OSS\Core\OssException
     */
    public function copyObject($fromObject,$toObject){
        $res = $this->ossConnect->copyObject($this->bucket,$fromObject,$this->bucket,$toObject);
        if($res){
            return true;
        }
        else{
            return false;
        }
    }

    //上传文件
    public function oldPathUploadFile($file_or_object,$tempPath)
    {
//        echo substr($tempPath,1,-1);
//        exit;
//        echo $file_or_object;
//        exit;
        $res = $this->ossConnect->uploadFile($this->bucket, substr($tempPath,1,strlen($tempPath)) ,$file_or_object, null);
        if (isset($res['info']['http_code'])) {
           if(isset($res['info']['http_code']) == 200){
               return true;
           }
        } else {
          return false;
        }
    }

    //上传文件
    public function uploadFile($file_or_object,$width = 0,$height = 0,int $file_type = TEMP_FILE_TYPE_IMAGE)
    {
        $this->fileType = $file_type;
        $this->uploadDirPath = $this->getPath();
        if ($file_or_object instanceof UploadedFile) {
            $this->fileSize = $file_or_object->getSize();
            $this->suffixName = $file_or_object->getClientOriginalExtension();
            $this->originalFilename = basename($file_or_object->getClientOriginalName(), "." . $this->suffixName);
            $this->filePath = $file_or_object->getRealPath();
        } else if (is_string($file_or_object)) {
            $this->fileSize = filesize($file_or_object);
            $this->suffixName = pathinfo(basename($file_or_object), PATHINFO_EXTENSION);
            $this->originalFilename = basename($file_or_object, "." . $this->suffixName);
            $this->filePath = $file_or_object;
        }
        $this->verifyFileSuffix();
        $this->width = $width;
        $this->height = $height;
        $this->fileName = Str::random(32) . "." . $this->suffixName;
        $this->tempDirPath = $this->getTempPath();
        $tempPath = $file_or_object;
        if ($file_type == TEMP_FILE_TYPE_IMAGE) {
            $tempPath = $this->tempDirPath . $this->fileName;
            $this->cutPictures($tempPath);
        }
        $res = $this->ossConnect->uploadFile($this->bucket, $this->uploadDirPath . $this->fileName, $tempPath, null);
        if (isset($res['info'])) {
            if (is_string($file_or_object)) {
                if(is_file($file_or_object)){
                    @unlink($file_or_object);
                }
            }
            return $this->result($res['info'], $file_or_object);
        } else {
            throw new HandleException('上传失败!');
        }
    }
    public function deleteObjects(array $data = []){
        $this->ossConnect->deleteObjects($this->bucket, $data);
    }

    //根据宽高裁剪图片并保存
    private function cutPictures($tempPath = '/'){
        $image = (new Image())::make($this->filePath);
        $height = $image->height();
        $width = $image->width();
        if(request()->get("compare_type") == 1){
            if($height != $width){
                @unlink($tempPath);
                throw new HandleException("图片尺寸必须为1:1如（宽20;高20）");
            }
        }
        if($this->width > 0 && $this->height > 0){
            $image->fit($this->width,$this->height);
        }
        else if($this->width > 0 || $this->height > 0){
            if($this->width > 0){
                $image->fit($this->width,intval(($height/$width)*$this->width));
            }
            if($this->height > 0){
                $image->fit(intval(($width/$height)*$this->height),$this->height);
            }
        }

        $this->width = $image->width();
        $this->height = $image->height();
        $image->save($tempPath);
    }

    //获取临时存储目录路径
    private function getTempPath(){
        $tempDir = storage_path('temp') . "/";
        if(!is_dir($tempDir)){
            mkdir($tempDir,0777,true);
        }
        return $tempDir;
    }

    //验证文件后缀名是否是允许的
    private function verifyFileSuffix(){
        switch ($this->fileType){
            case TEMP_FILE_TYPE_IMAGE:
                if(!in_array($this->suffixName,self::$allowed_image_extensions)){
                    throw new HandleException('图片格式不正确!');
                }
                break;
            case TEMP_FILE_TYPE_VIDEO:
                if(!in_array($this->suffixName,self::$allowed_video_extensions)){
                    throw new HandleException('视频格式不正确!');
                }
                break;
            case TEMP_FILE_TYPE_RADIO:
                if(!in_array($this->suffixName,self::$allowed_radio_extensions)){
                    throw new HandleException('音频格式不正确!');
                }
                break;
            case TEMP_FILE_TYPE_FILE:
                if(!in_array($this->suffixName,self::$allowed_file_extensions)){
                    throw new HandleException('文件格式不正确!');
                }
                break;
            default:
                throw new HandleException('文件类型不正确!');
        }
    }

    //返回结果
    private function result($res, $file_or_object){
        if($res['http_code'] == 200){
            if(is_file($this->tempDirPath. $this->fileName)){
                @unlink($this->tempDirPath. $this->fileName);
            }
            $result['url'] = getOssBindDomain($this->uploadDirPath.$this->fileName);
            $this->fileName = basename($this->fileName,".".$this->suffixName);
            $result['filename'] = $this->fileName;
            $this->saveTempFileData($file_or_object);
            return $result;
        }
        else{
            throw new HandleException('上传失败');
        }
    }

    //写入到临时存储文件数据表
    private function saveTempFileData($file_or_object){
        $TempFile = new TempFile();
        $TempFile->type = $this->fileType;
        $TempFile->filename = $this->fileName;
        $TempFile->original_filename = $this->originalFilename;
        $TempFile->path = getOssBindDomain($this->uploadDirPath.$this->fileName.".{$this->suffixName}");
        $info['size'] = $this->fileSize;
        $info['width'] = $this->width;
        $info['height'] = $this->height;
        if(exif_imagetype($file_or_object) == 2) {
            $imageInfo = exif_read_data($file_or_object, 0 ,true);
            if(isset($imageInfo['EXIF'])) {
                // 记录拍摄信息
                $info['time'] = $imageInfo['EXIF']['DateTimeOriginal'];
            }
            if(isset($imageInfo['IFD0'])) {
                // 记录拍摄设备
                $info['camera'] = $imageInfo['IFD0']['HostComputer'];
            }
            if(isset($imageInfo['GPS'])) {
                // 记录拍摄位置
                $latInfo = $imageInfo['GPS']['GPSLatitude'];
                $latOne = explode('/',$latInfo[0]);
                $latOne = $latOne[0] / $latOne[1];
                $latTwo = explode('/',$latInfo[1]);
                $latTwo = $latTwo[0] / $latTwo[1];
                $latThree = explode('/',$latInfo[2]);
                $latThree = $latThree[0] / $latThree[1];

                $info['lat'] = latToLat($latOne.','.$latTwo.','.$latThree);

                $longInfo = $imageInfo['GPS']['GPSLongitude'];
                $longOne = explode('/',$longInfo[0]);
                $longOne = $longOne[0] / $longOne[1];
                $longTwo = explode('/',$longInfo[1]);
                $longTwo = $longTwo[0] / $longTwo[1];
                $longThree = explode('/',$longInfo[2]);
                $longThree = $longThree[0] / $longThree[1];

                $info['long'] = latToLat($longOne.','.$longTwo.','.$longThree);
            }
            $TempFile->info = json_encode($info);
        }
        try{
            if(!$TempFile->save()){
                throw new HandleException('上传失败');
            }
        }
        catch (\Exception $e){
            throw new HandleException($e->getMessage());
        }
    }


}
