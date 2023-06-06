<?php


namespace App\Services\Api\Home\Column;


use App\Models\Column;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class Columns extends ApiBase implements ApiInterface
{
    private $Column;
    private $data;
    private $rules = [
        'target_type'   =>  'required|integer',
        'page'          =>  'integer|nullable',
        'count'         =>  'integer|nullable',
    ];
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
        $query->where('target_type',$this->data['target_type']);
        $query->orderBy('sort', 'desc');
        if(isset($this->data['page'])) {
            $data = $query->paginate($this->data['count'])->toArray();
        } else {
            $data['data'] = $query->get()->toArray();
        }
        $this->result = $data;
    }

    public function response(): array
    {
        return $this->successResponse();
    }
}
