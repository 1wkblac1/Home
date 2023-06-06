<?php


namespace App\Services\Api\Home\Column;


use App\Exceptions\HandleException;
use App\Exceptions\ParamsException;
use App\Models\Column;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class ColumnAdd extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'name'          =>  'required|string',
        'target_type'   =>  'required|integer',
        'status'        =>  'required|bool',
        'sort'          =>  'integer|nullable',
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
        $this->Column->target_type = $this->data['target_type'];
        $this->Column->name = $this->data['name'];
        $this->Column->status = $this->data['status'];
        $this->Column->sort = $this->data['sort'];
        if (!$this->Column->save()) {
            throw new HandleException('添加失败');
        }
    }

    public function response(): array
    {
        return $this->successResponse('添加成功');
    }
}
