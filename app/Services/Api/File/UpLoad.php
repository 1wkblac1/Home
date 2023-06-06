<?php


namespace App\Services\Api\File;


use App\Exceptions\ParamsException;
use App\Models\TempFile;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class UpLoad extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'type'      =>  'integer|required',
        'scale'     =>  'integer|nullable',
    ];

    private $object;
    private $uploadFileServer;
    private $tempFile;
    public function __construct(UploadFileServer $uploadFileServer, TempFile $tempFile)
    {
        $this->data = getRequest();
        $this->object = request()->file('file');
        $this->uploadFileServer = $uploadFileServer;
        $this->tempFile = $tempFile;
    }

    public function validate()
    {
        validate_input_or_exception($this->data, $this->rules);
        if(empty($this->object)) {
            throw new ParamsException('请选择要上传的文件');
        }
    }

    public function handle()
    {
        switch ($this->data['type']) {
            case TEMP_FILE_TYPE_IMAGE: // 记录照片的拍摄信息和位置信息
                $res = $this->uploadFileServer->uploadFile($this->object);
                // 封面图片
                $scale_res['url'] = $res['url'].'?x-oss-process=image/resize,h_200,m_lfit';
                // 关联到主图
                $query = $this->tempFile->newQuery();
                $query->where('filename', $res['filename']);
                $query->update(['scale_path' => $scale_res['url']]);
                break;
            case TEMP_FILE_TYPE_VIDEO: // 记录视频和制作封面
                $res = $this->uploadFileServer->uploadFile($this->object,0,0,TEMP_FILE_TYPE_VIDEO);
                // 封面图片
                $scale_res['url'] = $res['url'].'?x-oss-process=video/snapshot,t_0,f_jpg';
                // 关联到主图
                $query = $this->tempFile->newQuery();
                $query->where('filename', $res['filename']);
                $query->update(['scale_path' => $scale_res['url']]);
                break;
        }
        $res['type'] = $this->data['type'];
        $this->result = $res;
    }

    public function response(): array
    {
        return $this->successResponse();
    }
}
