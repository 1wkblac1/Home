<?php


namespace App\Services\Api\Home\Camera;


use App\Exceptions\HandleException;
use App\Exceptions\ParamsException;
use App\Models\Camera;
use App\Models\TempFile;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;
use App\Utils\UserCache;
use Illuminate\Support\Facades\DB;

class CameraAdd extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'column_id' =>  'integer|required',
        'files'     =>  'array|required',
        'sort'      =>  'integer|nullable',
        'type'      =>  'integer|required',
        'describe'  =>  'string|nullable',
        'title'     =>  'string|required',
        'sub_title' =>  'string|nullable',
        'status'    =>  'required|bool',
    ];

    private $Camera;
    private $TempFile;
    private $CameraDetail;
    public function __construct(Camera $camera, TempFile $tempFile, \App\Models\CameraDetail $cameraDetail)
    {
        $this->data = getRequest();
        $this->Camera = $camera;
        $this->TempFile = $tempFile;
        $this->CameraDetail = $cameraDetail;
    }

    public function validate()
    {
        validate_input_or_exception($this->data, $this->rules);
        if (strlen($this->data['sort']) > 4) {
            throw new ParamsException('排序区间为（0-9999）');
        }
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->Camera->column_id = $this->data['column_id'];
            $this->Camera->title = $this->data['title'];
            $this->Camera->sub_title = $this->data['sub_title'] ? $this->data['sub_title'] : $this->data['title'];
            $this->Camera->type = $this->data['type'];
            $this->Camera->admin_id = UserCache::getUserField();
            $this->Camera->author = UserCache::getUserField('nickname');
            $this->Camera->status = $this->data['status'];
            $this->Camera->sort = $this->data['sort'];
            $this->Camera->describe = $this->data['describe'];

            if(!$this->Camera->save()){
                throw new HandleException('发布失败[0001]');
            }

            foreach ($this->data['files'] as $v) {
                $fileInfo = $this->TempFile->where('filename', $v['filename'])->first();
                if (empty($fileInfo)) {
                    throw new HandleException('图片/视频信息不存在');
                }
                $address = [];
                $take_time = date('Y-m-d');
                $take_tool = '';
                if (isset($fileInfo->info['lat'])) {
                    $address = [
                        'lat' => $fileInfo->info['lat'],
                        'lng' => $fileInfo->info['lng']
                    ];
                    $take_time = $fileInfo->info['time'];
                    $take_tool = $fileInfo->info['camera'];
                }
                $arr[] = [
                    'camera_id'     => $this->Camera->id,
                    'filename'      => $v['filename'],
                    'path'          => $fileInfo->path,
                    'address'       => json_encode($address),
                    'take_time'     => $take_time,
                    'take_tool'     => $take_tool,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
            }

            if (!$this->CameraDetail->addAll($arr)) {
                throw new HandleException('发布失败[0002]');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HandleException($e->getMessage());
        }
    }

    public function response(): array
    {
        return $this->successResponse('发布成功');
    }
}
