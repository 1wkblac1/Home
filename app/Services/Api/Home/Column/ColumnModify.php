<?php


namespace App\Services\Api\Home\Column;


use App\Exceptions\HandleException;
use App\Exceptions\NoTargetException;
use App\Exceptions\ParamsException;
use App\Models\Column;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class ColumnModify extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'id'        =>  'required|integer',
        'name'      =>  'required|string',
        'status'    =>  'required|boolean',
        'sort'      =>  'nullable|integer',
    ];
    private $Column;
    public function __construct(Column $column)
    {
        $this->data = getRequest();
        $this->Column = $column;
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
        $query = $this->Column->newQuery();
        $info = $query->where('id', $this->data['id'])->first();
        if (!$info) {
            throw new NoTargetException();
        }
        $info->name = $this->data['name'];
        $info->status = $this->data['status'];
        $info->sort = $this->data['sort'];
        if (!$info->save()) {
            throw new HandleException('更新失败');
        }

    }

    public function response(): array
    {
        return $this->successResponse('更新成功');
    }
}
