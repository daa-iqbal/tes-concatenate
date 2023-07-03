<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailExperience extends Model
{
    protected $guarded = [];
    public function paket(){
        return $this->morphTo();
    }

    public function image_file(){
        return $this->hasOne('App\Models\File','id','image_file_id')->select('id','full_path','full_path_thumbnail');
    }
}

