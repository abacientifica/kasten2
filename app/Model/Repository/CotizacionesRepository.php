<?php

namespace App\Model\Repository;

use Illuminate\Database\Eloquent\Model;
use App\Model\Repository\BaseRepository;
use App\Model\Cotizaciones;

class CotizacionesRepository extends BaseRepository
{
    public function __constructor(Cotizaciones $model){
        parent::__constructor($model);
    }
}
