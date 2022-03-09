<?php

namespace App\Model\Repository;

use Illuminate\Database\Eloquent\Model;
use App\Model\Repository\BaseRepository;
use App\Model\TempVentasGeneral;
use Illuminate\Support\Facades\DB;

class ReportesVentasRepository extends BaseRepository
{
    public function __construct(TempVentasGeneral $model){
        parent::__construct($model);
    }

    public function getVentasReporte()
    {
        return $this->dbSelect("SELECT * FROM temp_ventas_general WHERE IdUsuario ='kasten' and  DATE_FORMAT(FechaMvto,'%Y') = DATE_FORMAT(NOW(),'%Y')");
    }

    public function getVentasByFilters($filtros)
    {
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
        ] = $filtros);

        $builder = $this->getQuery()
        ->select(DB::raw("CONCAT(DATE_FORMAT(FechaMvto,'%b'),'- $',format(SUM(SubTotal),2))as Mes"), DB::raw('YEAR(FechaMvto) as Anio'),DB::raw('SUM(SubTotal) as Total'),DB::raw('MONTH(FechaMvto) as Mes2'))
        ->where('IdUsuario','kasten')
        ->when($anio,function($builder,$anio){
            return $builder->where(DB::raw('year(`FechaMvto`)'),$anio);
        })
        ->when($nitCliente,function($builder,$nitCliente){
            return $builder->where('IdCliente',$nitCliente);
        })
        ->when($idterceroProv,function($builder,$idterceroProv){
            return $builder->where('IdProveedor',$idterceroProv);
        })
        ->when($nmMarca,function($builder,$nmMarca){
            return $builder->where('NmMarca','like','%'.$nmMarca.'%');
        })
        ->when($idLinea,function($builder,$idLinea){
            return $builder->where('IdLinea',$idLinea);
        })
        ->when($idGrupo,function($builder,$idGrupo){
            return $builder->where('IdGrupo',$idGrupo);
        })
        ->when($idSubGrupo,function($builder,$idSubGrupo){
            return $builder->where('IdSubGrupo',$idSubGrupo);
        })
        ->when($idAsesor,function($builder,$idAsesor){
            return $builder->where('IdAsesorComision',$idAsesor);
        })
        ->when($descripcion,function($builder,$descripcion){
            return $builder->where('Descripcion','like','%'.$descripcion.'%');
        })
        ->when($idItem,function($builder,$idItem){
            return $builder->where('Id_Item',$idItem);
        })
        ->groupBy( DB::raw('MONTHNAME(FechaMvto)'), DB::raw('YEAR(FechaMvto)'))
        ->orderBy(DB::raw('MONTH(FechaMvto)'));
        return $builder->get();
    }

    public function getVentasVsAnioAnterior($filtros){
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
        ] = $filtros);
        $builder = $this->getQuery()
        ->select(DB::raw("DATE_FORMAT(FechaMvto,'%b') as Mes"), DB::raw('YEAR(FechaMvto) as Anio'),DB::raw('SUM(SubTotal) as Total'),DB::raw('MONTH(FechaMvto) as Mes2'))
        ->where('IdUsuario','kasten')
        ->when($anio,function($builder,$anio){
            return $builder->where(function($builder) use ($anio){
                return $builder->where(DB::raw('year(`FechaMvto`)'),$anio)->orWhere(DB::raw('year(`FechaMvto`)'),($anio)-1);
            });
        })
        
        ->when($nitCliente,function($builder,$nitCliente){
            return $builder->where('IdCliente',$nitCliente);
        })
        ->when($idterceroProv,function($builder,$idterceroProv){
            return $builder->where('IdProveedor',$idterceroProv);
        })
        ->when($nmMarca,function($builder,$nmMarca){
            return $builder->where('NmMarca','like','%'.$nmMarca.'%');
        })
        ->when($idLinea,function($builder,$idLinea){
            return $builder->where('IdLinea',$idLinea);
        })
        ->when($idGrupo,function($builder,$idGrupo){
            return $builder->where('IdGrupo',$idGrupo);
        })
        ->when($idSubGrupo,function($builder,$idSubGrupo){
            return $builder->where('IdSubGrupo',$idSubGrupo);
        })
        ->when($idAsesor,function($builder,$idAsesor){
            return $builder->where('IdAsesorComision',$idAsesor);
        })
        ->when($descripcion,function($builder,$descripcion){
            return $builder->where('Descripcion','like','%'.$descripcion.'%');
        })
        ->when($idItem,function($builder,$idItem){
            return $builder->where('Id_Item',$idItem);
        })
        ->groupBy( DB::raw('MONTHNAME(FechaMvto)'), DB::raw('YEAR(FechaMvto)'))
        ->orderBy(DB::raw('MONTH(FechaMvto),year(`FechaMvto`)'),'ASC');
        return $builder->get();
    }





}
