<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CameraDetail extends Model
{
    use SoftDeletes;

    public function addAll($data) {
        return DB::table($this->getTable())->insert($data);
    }
}
