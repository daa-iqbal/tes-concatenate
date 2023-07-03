<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenVerification extends Model
{
    //

    protected $fillable = [
    	'id','id_user','token_verification'
    ];
}
