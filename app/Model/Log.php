<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table="log";
    public $timestamps = false;
    public $primarykey = 'IdLog';
}
