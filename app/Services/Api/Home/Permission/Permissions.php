<?php


namespace App\Services\Api\Home\Permission;


use App\Models\Admin;
use App\Services\Api\ApiBase;
use App\Services\Api\ApiInterface;
use App\Utils\UserCache;

class Permissions extends ApiBase implements ApiInterface
{
    public function __construct()
    {
    }

    public function validate()
    {
    }

    public function handle()
    {
        $adminModel = Admin::query();
        $data = $adminModel->where('id',UserCache::getUserField())->first()->toArray();
        $this->result = $data;
    }

    public function response(): array
    {
        return $this->successResponse();
    }
}
