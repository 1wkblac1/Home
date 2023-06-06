<?php


namespace App\Models\Selection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Selection extends Model
{
    public function addAll(Array $data)
    {
        return DB::table($this->getTable())->insert($data);
    }
}
