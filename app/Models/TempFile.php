<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempFile extends Model
{
    public function getInfoAttribute($value) {
        return !empty($value) ? json_decode($value, true) : [];
    }
}
