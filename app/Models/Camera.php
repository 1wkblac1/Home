<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'cover_path',
        'images',
        'files',
        'column_name'
    ];

    public function setSortAttribute($value) {
        $this->attributes['sort'] = !empty($value) ? $value : 0;
    }

    public function setAddressAttribute($value) {
        $this->attributes['address'] = json_encode($value);
    }

    public function getStatusAttribute($value)
    {
        return $value ? true :false;
    }

    public function getColumnNameAttribute() {
        return $this->hasOne(Column::class, 'id', 'column_id')->value('name');
    }

    public function getCoverPathAttribute() {
        if ($this->type === TEMP_FILE_TYPE_IMAGE) {
            return $this->hasOne(CameraDetail::class, 'camera_id', 'id')->value('path');
        } else if ($this->type === TEMP_FILE_TYPE_VIDEO) {
            $filename = $this->hasOne(CameraDetail::class, 'camera_id', 'id')->value('filename');
            return TempFile::where('filename', $filename)->value('scale_path');
        }
    }

    public function getImagesAttribute() {
        if ($this->type === TEMP_FILE_TYPE_IMAGE) {
            return $this->hasOne(CameraDetail::class, 'camera_id', 'id')->pluck('path')->toArray();
        } else if ($this->type === TEMP_FILE_TYPE_VIDEO) {
            $filename = $this->hasOne(CameraDetail::class, 'camera_id', 'id')->value('filename');
            return TempFile::where('filename', $filename)->pluck('scale_path')->toArray();
        }
    }

    public function getFilesAttribute() {
        $urls = $this->hasOne(CameraDetail::class, 'camera_id', 'id')->pluck('path')->toArray();
        $filenames = $this->hasOne(CameraDetail::class, 'camera_id', 'id')->pluck('filename')->toArray();
        foreach ($urls as $k => $v) {
            $data[$k] = [
                'url'   =>  $v,
                'filename'   =>  $filenames[$k],
                'type'  =>  $this->type,
            ];
        }
        return $data;
    }
}
