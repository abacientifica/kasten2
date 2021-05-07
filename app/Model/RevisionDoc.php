<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RevisionDoc extends Model
{
    protected $table = "revision_doc";
    public $timestamps = false;
    public $primarykey = 'IdRevision';
}

