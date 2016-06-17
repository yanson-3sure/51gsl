<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWechat extends Model
{
    protected $table = 'user_wechats';
    public $primaryKey = 'unionid';
    public $timestamps = false;
}
