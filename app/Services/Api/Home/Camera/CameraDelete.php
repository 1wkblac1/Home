<?php


namespace App\Services\Api\Home\Camera;


use App\Exceptions\HandleException;
use App\Models\Camera;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class CameraDelete extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'id'    =>  'required|integer',
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
    }

    public function handle()
    {
        $query = $this->Camera->newQuery();
        if (!$query->where('id', $this->data['id'])->delete()) {
            throw new HandleException('删除失败');
        }
    }

    public function response(): array
    {
        return $this->successResponse('删除成功');
    }
}

