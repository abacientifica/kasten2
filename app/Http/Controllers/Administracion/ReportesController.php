<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Repository\ReportesVentasRepository;

class ReportesController extends Controller
{
    public $rep;
    public function __construct(ReportesVentasRepository $repository){
        $this->rep = $repository;
    }

    public function reporteVentas(Request $request)
    {
        $request->validate([
            'anioInicial'=>'required | numeric'
        ]);

        $filtros = ([
            "anioInicial" => $anio,
            "nitCliente"=>$nitCliente,
            "idterceroProv"=>$idterceroProv,
            "nmMarca"=>$nmMarca,
            "idLinea"=>$idLinea,
            "idGrupo"=>$idGrupo,
            "idSubGrupo"=>$idSubGrupo,
            "idAsesor"=>$idAsesor,
            "descripcion"=>$descripcion,
            "idItem"=>$idItem
        ] = $request->all());

        return[
            'ventas'=>$this->rep->getVentasByFilters($filtros)
        ];
    }
}
