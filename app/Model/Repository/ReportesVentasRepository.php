<?php

namespace App\Model\Repository;

use Illuminate\Database\Eloquent\Model;
use App\Model\Repository\BaseRepository;
use App\Model\TempVentasGeneral;

class ReportesVentasRepository extends BaseRepository
{
    public function __construct(TempVentasGeneral $model){
        parent::__construct($model);
    }

    public function getVentasReporte(){
        return $this->dbSelect("SELECT * FROM temp_ventas_general WHERE IdUsuario ='kasten' and FechaMvto >= DATE_SUB(NOW(), interval 28 month)");
    }

}
