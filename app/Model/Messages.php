<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    protected $table="messages";

    public function remitente(){
        return $this->hasOne('App\User','Usuario','from');
    }

    public function destinatario(){
        return $this->hasOne('App\User','Usuario','to');
    }
}
