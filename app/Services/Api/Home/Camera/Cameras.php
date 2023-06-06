<?php


namespace App\Services\Api\Home\Camera;


use App\Models\Camera;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;

class Cameras extends ApiBase implements ApiInterface
{
    private $data;
    private $rules = [
        'column_id'     =>  'integer|nullable',
        'keyword'       =>  'string|nullable',
        'page'          =>  'integer|nullable',
        'count'         =>  'integer|nullable',
    ];

    private $Camera;
    public function __construct(Camera $camera)
    {
        $this->data = getRequest();
        $this->Camera = $camera;
    }

    public function validate()
    {
        validate_input_or_exception($this->data,$this->rules);
    }

    public function handle()
    {
        $query = $this->Camera->newQuery();
        $query->when(!empty($this->data['column_id']), function ($query) {
           return $query->where('column_id', $this->data['column_id']);
        });
        $query->when(!empty($this->data['keyword']), function ($query) {
            $query->where('title','like','%'.$this->data['keyword'].'%')
                ->orWhere('sub_title','like','%'.$this->data['keyword'].'%');
        });
        $query->orderBy('sort','desc');
        $query->orderBy('id','desc');
        if (isset($this->data['page'])) {
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
