<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Praise extends Model
{
    //
    use SoftDeletes;
    public $timestamps = false;
}
