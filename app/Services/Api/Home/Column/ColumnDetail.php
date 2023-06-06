<?php


namespace App\Services\Api\Home\Column;

use App\Exceptions\NoTargetException;
use App\Models\Column;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class ColumnDetail extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'id'    =>  'required|integer'
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
        $info = $query->where('id', $this->data['id'])->first();
        if (!$info) {
            throw new NoTargetException();
        }
        $this->result = $info->toArray();
    }

    public function response(): array
    {
        return $this->successResponse();
    }
}
