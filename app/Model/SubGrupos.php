<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubGrupos extends Model
{
    protected $table ="subgrupos";
    protected $primaryKey =["IdSubgrupo","IdGrupo","IdLinea"];
    public $incrementing = false;
    protected $keyType ="string";
}
