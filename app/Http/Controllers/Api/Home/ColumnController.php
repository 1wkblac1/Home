<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Services\Api\Home\Column;

class ColumnController extends Controller
{
    // 栏目列表
    public function columns() {
        return api(Column\Columns::class);
    }

    // 栏目新增
    public function columnAdd() {
        return api(Column\ColumnAdd::class);
    }

    // 栏目修改
    public function columnModify() {
        return api(Column\ColumnModify::class);
    }

    // 栏目详情
    public function columnDetail() {
        return api(Column\ColumnDetail::class);
    }

    // 栏目删除
    public function columnDelete() {
        return api(Column\ColumnDelete::class);
    }
}
