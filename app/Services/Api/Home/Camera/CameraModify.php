<?php


namespace App\Services\Api\Home\Camera;


use App\Exceptions\HandleException;
use App\Exceptions\NoTargetException;
use App\Exceptions\ParamsException;
use App\Models\Camera;
use App\Models\TempFile;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;
use App\Utils\UserCache;
use Illuminate\Support\Facades\DB;

class CameraModify extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'id'        =>  'integer|required',
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
    public function __construct(Camera $camera, \App\Models\CameraDetail $cameraDetail, TempFile $tempFile)
    {
        $this->data = getRequest();
        $this->Camera = $camera;
        $this->CameraDetail = $cameraDetail;
        $this->TempFile = $tempFile;
    }

    public function validate()
    {
        validate_input_or_exception($this->data, $this->rules);
        if (!empty($this->data['sort']) && strlen($this->data['sort']) > 4) {
            throw new ParamsException('排序区间为（0-9999）');
        }
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $query = $this->Camera->newQuery();
            $info = $query->where('id', $this->data['id'])->first();
            if (!$info) {
                throw new NoTargetException();
            }
            $info->column_id = $this->data['column_id'];
            $info->title = $this->data['title'];
            $info->sub_title = $this->data['sub_title'];
            $info->type = $this->data['type'];
            $info->admin_id = UserCache::getUserField();
            $info->author = UserCache::getUserField('nickname');
            $info->status = $this->data['status'];
            $info->sort = $this->data['sort'];
            $info->describe = $this->data['describe'];

            if (!$info->save()){
                throw new HandleException('发布失败[0001]');
            }

            // 清除之前的图片
            if (!$this->CameraDetail->where(['camera_id' => $info->id])->delete()) {
                throw new HandleException('发布失败[0002]');
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
                    'camera_id'     => $info->id,
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
                throw new HandleException('发布失败[0003]');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HandleException($e->getMessage());
        }
    }

    public function response(): array
    {
        return $this->successResponse('更新成功');
    }
}
