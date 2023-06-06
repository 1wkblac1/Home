<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Column extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'target_type',
    ];

    public function getStatusAttribute($value)
    {
        return $value ? true :false;
    }

    public function setSortAttribute($value)
    {
        $this->attributes['sort'] = !empty($value) ? $value : 0;
    }
}
