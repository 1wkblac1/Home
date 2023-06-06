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

class CameraListModify extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'id'        =>  'integer|required',
        'sort'      =>  'integer|required',
        'status'    =>  'bool|required',
    ];

    private $Camera;
    public function __construct(Camera $camera)
    {
        $this->data = getRequest();
        $this->Camera = $camera;
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

            $info->sort = $this->data['sort'];
            $info->status = $this->data['status'];
            if (!$info->save()){
                throw new HandleException('修改失败');
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
