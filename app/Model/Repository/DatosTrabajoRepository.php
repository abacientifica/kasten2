<?php

namespace App\Model\Repository;

use Illuminate\Database\Eloquent\Model;
use App\Model\Repository\BaseRepository;
use App\Model\DatosTrabajoKasten;

class DatosTrabajoRepository extends BaseRepository
{
    public function __construct(DatosTrabajoKasten $model)
    {
        parent::__construct($model);
    }

    public function getFilterUserByDoc($user,$iddoc)
    {
        $builder = $this->getQuery()
        ->when($user,function($builder,$user){
            return $builder->where('IdUsuario',$user);
        })
        ->when($iddoc,function($builder,$iddoc){
            return $builder->where('IdDocumento',$iddoc);
        });
        return $builder->get();
    }
}
