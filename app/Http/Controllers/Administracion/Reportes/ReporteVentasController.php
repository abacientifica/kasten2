<?php

namespace App\Http\Controllers\Administracion\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Repository\ReportesVentasRepository;

class ReporteVentasController extends Controller
{
    public $rep;

    public function __construct(ReportesVentasRepository $repository){
        $this->rep = $repository;
    }

    public function __invoke(Request $request)
    {
        return[
            'columnas'=> \Funciones::getConfiguracionGrillaDet(94),
            'datos'=>$this->rep->getVentasReporte(),
            'status'=>200
        ];
    }
}
