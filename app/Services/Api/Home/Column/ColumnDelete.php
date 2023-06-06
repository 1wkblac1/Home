<?php


namespace App\Services\Api\Home\Column;


use App\Exceptions\HandleException;
use App\Models\Column;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class ColumnDelete extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'id'    =>  'required|integer',
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
    }

    public function handle()
    {
        $query = $this->Column->newQuery();
        if (!$query->where('id', $this->data['id'])->delete()) {
            throw new HandleException('删除失败');
        }
    }

    public function response(): array
    {
        return $this->successResponse('删除成功');
    }
}
