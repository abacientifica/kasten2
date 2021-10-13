<?php

namespace App\Http\Controllers\Plantillas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Pipeline\Pipeline;
use App\Model\Plantillas;
use App\Model\PlantillasDet;
use App\Model\Cotizaciones;
use App\Model\ListaCostosProvDet;
use App\Task\QuitarCaracteresString;
use App\Task\FormatearPrecios;
use App\Events\RegistrarLog;


class PlantillasClientesController extends Controller
{
    public function ListaPlantillas(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $Id = isset($request->Id) ? $request->Id : null;
            $Estado = isset($request->Estado) ? $request->Estado : null;
            $Cliente = isset($request->IdTercero) ? $request->IdTercero : null;
            $oRangoFechas = isset($request->oRangoFechas) ? $request->oRangoFechas : null;
            $Plantillas = Plantillas::with('tercero','direccion')->where('Tipo',0);
            if($Id){
                //$Plantillas = $Plantillas->where("IdPlantilla",$Id);
            }
            if($Estado){
                $Plantillas = $Plantillas->where("Estado",$Estado);
            }
            if($Cliente){
                $Plantillas = $Plantillas->where("IdTerceroPlant",$Cliente);
            }
            if($oRangoFechas){
                //$Plantillas = $Plantillas->whereBetween('FhAutoriza',[$Filtros->oRangoFechas[0],$Filtros->oRangoFechas[1]]);
            }
            $Plantillas = $Plantillas->OrderBy('FhPlantilla','DESC')->get();
            return[
                'plantillas'=>$Plantillas
            ];
        }
        catch(Exception $e){
            return[
                'error'=>$e->getMessage()
            ];
        }
    }

    public function ObtenerPlantilla(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $Id = isset($request->Id) ? $request->Id : null;
            $Filtros = isset($request->filtros) ? $request->filtros : null; 
            $Filtros = ($Filtros) ? json_decode($Filtros) : null;
            $Plantillas = Plantillas::with('tercero','direccion','plantillasdet')->find($Id);
            $PlantillasDet = \Funciones::CargarDetallesPlantillaClientes($Id,93,$Filtros);
            $DatosHomologar = \Funciones::ObtenerDatosRestablecerCostosPlantillas($Plantillas->IdPlantilla,$Plantillas->IdTerceroPlant);
            $ConfiguracionesGrilla = \Funciones::ObtenerConfiguracionesGrilla(93,true);
            $ConfiguracionesGrillaDet = \Funciones::ObtenerConfiguracionesGrilla(93);
            return[
                'plantilla'=>$Plantillas,
                'plantillas_det'=>$PlantillasDet['plantillas_det'],
                'columnas'=>$PlantillasDet['columnas'],
                'filtros'=>$Filtros,
                'datos_listas'=>$DatosHomologar,
                'configuraciones'=>$ConfiguracionesGrilla,
                'configuraciones_det'=>$ConfiguracionesGrillaDet,
            ];
        }
        catch(Exception $e){
            return[
                'error'=>$e->getMessage()
            ];
        }
    }

    public function Autorizar(Request $request) {
        if(!$request->ajax()) return redirect("/");
        try{
            $Plantilla = Plantillas::find($request->params['nIdPlantilla']);
            $Plantilla->Estado = 'AUTORIZADA';
            if ($Plantilla->save()) {
                \Funciones::CrearLogPlantillas(2, $Plantilla->IdPlantilla,null,'',null);
                return [
                    'plantilla'=>$Plantilla,
                    'msg'=>"La plantilla ha sido Auitorizada con exito ".$Plantilla->IdPlantilla,
                    'status'=>201
                ];
            }
        }
        catch(Exception $e){
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        } 
    }

    public function DesAutorizar(Request $request) {
        if(!$request->ajax()) return redirect("/");
        try{
            $Plantilla = Plantillas::find($request->params['nIdPlantilla']);
            $Plantilla->Estado = 'DIGITADA';
            if ($Plantilla->save()) {
                \Funciones::CrearLogPlantillas(2, $Plantilla->IdPlantilla,null,'',null);
                return [
                    'plantilla'=>$Plantilla,
                    'msg'=>"La plantilla ha sido Des-Autorizada con exito ".$Plantilla->IdPlantilla,
                    'status'=>201
                ];
            }
        }
        catch(Exception $e){
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        } 
    }

    public function AutorizarDetalles(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $Datos = $request->params['arrItems'];
            $IdPlantilla = $request->params['nIdPlantilla'];
            $Cont =0;
            foreach($Datos as $row){
                if($row['Autorizado'] != 1){
                    $Det = PlantillasDet::find($row['IdPlantillaDet']);
                    $Det->Autorizado = 1;
                    $Det->save();
                    \Funciones::CrearLogPlantillas(61, $Det->IdPlantilla, $Det->IdPlantillaDet,'',$Det->ItemAba);
                }
                $Cont++;
            }
            DB::commit();
            return [
                'msg'=>"Los detalles han sido autorizados.",
                'status'=>201,
                'daa'=>$Datos
            ];
        }
        catch(ErrorException $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage(),
            ];
        }
    }

    public function DesAutorizarDetalles(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $Datos = $request->params['arrItems'];
            $IdPlantilla = $request->params['nIdPlantilla'];
            $Cont =0;
            foreach($Datos as $row){
                if($row['Autorizado'] == 1){
                    $Det = PlantillasDet::find($row['IdPlantillaDet']);
                    $Det->Autorizado = 0;
                    $Det->save();
                    \Funciones::CrearLogPlantillas(62, $Det->IdPlantilla, $Det->IdPlantillaDet,'',$Det->ItemAba);
                }
                $Cont++;
            }
            DB::commit();
            return [
                'msg'=>"Los detalles han sido des-autorizados",
                'status'=>201,
                'daa'=>$Datos
            ];
        }
        catch(ErrorException $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage(),
            ];
        }
    }

    public function Anular(Request $request) {
        if(!$request->ajax()) return redirect("/");
        try{
            $Plantilla = Plantillas::find($request->params['nIdPlantilla']);
            $Plantilla->Estado = 'ANULADA';
            if ($Plantilla->save()) {
                \Funciones::CrearLogPlantillas(1, $Plantilla->IdPlantilla,null,'',null);
                return [
                    'plantilla'=>$Plantilla,
                    'msg'=>"La plantilla ha sido Anulada con exito ".$Plantilla->IdPlantilla,
                    'status'=>201
                ];
            }
        }
        catch(Exception $e){
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        } 
    }

    public function CrearPlantilla(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            DB::beginTransaction();
            $Datos = $request->params['fillNuevaPlantilla'];
            $Plantilla = new Plantillas();
            $Plantilla->IdTerceroPlant = $Datos['nIdTercero'];
            $Plantilla->IdDireccionPlant = $Datos['nIdDireccion'];
            $Plantilla->NmPlantilla = $Datos['cNmPlantilla'];
            $Plantilla->FhEntregaPropuesta = $Datos['FechaEntregaPropuesta'];
            //$Plantilla->Periodo =  $Datos['dPeriodoAnio']."-".$Datos['dPeriodoMes'];
            $Plantilla->Estado = 'DIGITADA';
            $Plantilla->Usuario = \Auth::user()->Usuario;
            $Plantilla->Comentarios = $Datos['cComentarios'];
            $Plantilla->CantidadConsumoMes = $Datos['nMesesConsumo'];
            $Plantilla->FhPlantilla = date('Y-m-d H:i:s');
            $Plantilla->VigDesde = $Datos['oVigenciaOferta'][0];
            $Plantilla->VigHasta = $Datos['oVigenciaOferta'][1];
            $Plantilla->Tipo =0;
            $Plantilla->save();
            DB::commit();
            \Funciones::CrearLogPlantillas(8, $Plantilla->IdPlantilla);
            return [
                'plantilla'=>$Plantilla->IdPlantilla,
                'msg'=>"La plantilla ha sido creada con exito ".$Plantilla->IdPlantilla,
                'status'=>201
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        } 
    }

    public function ActualizarPlantilla(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $Datos = $request->params['fillEditarPlantilla'];
            $Plantilla = Plantillas::find($Datos['nIdPlantilla']);
            $Plantilla->IdTerceroPlant = $Datos['nIdTercero'];
            $Plantilla->IdDireccionPlant = $Datos['nIdDireccion'];
            $Plantilla->NmPlantilla = $Datos['cNmPlantilla'];
            $Plantilla->FhEntregaPropuesta = $Datos['FechaEntregaPropuesta'];
            $Plantilla->Periodo =  $Datos['dPeriodoAnio']."-".$Datos['dPeriodoMes'];
            $Plantilla->Estado = 'DIGITADA';
            $Plantilla->Comentarios = $Datos['cComentarios'];
            $Plantilla->CantidadConsumoMes = $Datos['nMesesConsumo'];
            $Plantilla->VigDesde = $Datos['oVigenciaOferta'][0];
            $Plantilla->VigHasta = $Datos['oVigenciaOferta'][1];
            $Plantilla->Tipo =0;
            $Plantilla->save();
            DB::commit();
            \Funciones::CrearLogPlantillas(16, $Plantilla->IdPlantilla);
            return [
                'plantilla'=>$Plantilla->IdPlantilla,
                'msg'=>"La plantilla ha sido editada con exito ".$Plantilla->IdPlantilla,
                'plantilla'=>$Plantilla,
                'status'=>201
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        }
    }

    public function EliminarDetalles(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $Datos = $request->params['arrItemsEliminar'];
            $IdPlantilla = $request->params['nIdPlantilla'];
            $Cont =0;
            foreach($Datos as $row){
                if($row['Autorizado'] != 1){
                    $Det = PlantillasDet::find($row['IdPlantillaDet']);
                    \Funciones::CrearLogPlantillas(19, $Det->IdPlantilla, $Det->IdPlantillaDet,'',$Det->ItemAba);
                    $Det->delete();
                }
                $Cont++;
            }
            \Funciones::ActualizarDatosPlantillaClientes($IdPlantilla);
            DB::commit();
            return [
                'msg'=>"Los detalles han sido eliminados o de lo contrario valida que no esten autorizados.",
                'status'=>201,
                'daa'=>$Datos
            ];
        }
        catch(ErrorException $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage(),
            ];
        }
    }

    public function CargarDatosHomologacion(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $Filtros = $request->Filtros;
        $Criterios = $request->Criterios;
        $IdTercero = $request->IdTercero;
        $Sql = \Funciones::DatosHomologarPlantilla($IdTercero,$Criterios,$Filtros);
        $Datos = DB::select($Sql);
        return[
            'listas_det'=>$Datos,
            'sql'=>$Sql
        ];
    }

    public function AsignarLista(Request $request) {
        if(!$request->ajax()) return  redirect('/');
        $PlantDet = new PlantillasDet();
        $PlantDet = PlantillasDet::find($request->params['IdPlantillaDet']);
        $IdItem = $request->params['Item'];
        $PlantDet->IdListaCostosDetPlantDet = $request->params['IdLista'];
        if ($PlantDet->save()) {
            event(new RegistrarLog(['Tipo'=>1,'IdAccion'=>42,'Id'=>$PlantDet->IdPlantilla,'IdDet'=>$PlantDet->IdPlantillaDet,'IdItem'=>$IdItem,'Comentarios'=>'Homologar '.$IdItem]));
            \Funciones::ActualizarDatosPlantillaClientes($PlantDet->IdPlantilla,$PlantDet->IdPlantillaDet);
            return[
                'status'=>201,
                'msg'=>"El detalle ha sido homologado"
            ];
        }
        else{
            return[
                'status'=>500,
                'msg'=>"Ocurrio un error"
            ];
        }
    }

    public function DesligarLista(Request $request) {
        if(!$request->ajax()) return  redirect('/');
        $PlantDet = new PlantillasDet();
        $PlantDet = PlantillasDet::find($request->params['IdPlantillaDet']);
        $IdItem = $request->params['IdItem'];
        $PlantDet->IdListaCostosDetPlantDet = null;
        if ($PlantDet->save()) {
            event(new RegistrarLog(['Tipo'=>1,'IdAccion'=>42,'Id'=>$PlantDet->IdPlantilla,'IdDet'=>$PlantDet->IdPlantillaDet,'IdItem'=>$IdItem,'Comentarios'=>'Des-Homologar '.$IdItem]));
            \Funciones::ActualizarDatosPlantillaClientes($PlantDet->IdPlantilla,$PlantDet->IdPlantillaDet);
            $PlantillaDet = \Funciones::CargarDetallesPlantillaClientes($PlantDet->IdPlantilla,93,' and plantillas_det.IdPlantillaDet = '.$PlantDet->IdPlantillaDet);
            $PlantillaDet = $PlantillaDet['plantillas_det'];
            return[
                'status'=>201,
                'msg'=>"El detalle ha sido des-homologado",
                'detalle'=>$PlantillaDet[0]
            ];
        }
        else{
            return[
                'status'=>500,
                'msg'=>"Ocurrio un error"
            ];
        }
    }

    public function GuardarDatosEdit(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $PlantillaDet = PlantillasDet::find($request->params['IdPlantillaDet']);
            $DatosEdit = $request->params['arrCambios'];
            foreach($DatosEdit as $key=>$dato){
                $Columna = $dato['Columna'];
                $DatoNuevo = $dato['DatoNuevo'];
                $DatoAnt = isset($dato['DatoAnt']) ? $dato['DatoAnt'] : '';
                $PlantillaDet->$Columna = $DatoNuevo;
                $PlantillaDet->save();
                $Comentario = "La columna '".$Columna."'  dato anterior era  = ".$DatoAnt." y el dato nuevo es = ".$DatoNuevo;
                \Funciones::CrearLogPlantillas(16,$PlantillaDet->IdPlantilla,$PlantillaDet->IdPlantillaDet,$Comentario,$PlantillaDet->ItemAba);
                $ActualizarDetalle = \Funciones::ActualizarDatosPlantillaClientes($PlantillaDet->IdPlantilla,$PlantillaDet->IdPlantillaDet);
                if(!$ActualizarDetalle) throw new Exception("Ocurrio un error al actualizar los datos");
            }
            DB::commit();
            return [
                'msg'=>"Los datos del detalle ".$request->params['IdPlantillaDet']." han sido modificados!!",
                'status'=>201
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        }
    }

    public function MarcarItemsVendidos(Request $request) {
        if(!$request->ajax()) return  redirect('/');
        try{
            $Plantilla =  $request->params['arrPlantilla'];
            if($Plantilla){
                $RangoFechas = $request->params['oRangoFecha'];
                $sql = "SELECT movimientos_det.Id_Item
                    FROM movimientos_det
                    WHERE movimientos_det.TpDocumento=5 and (movimientos_det.estado='AUTORIZADO' OR movimientos_det.estado='CERRADO') and IdTercero=" . $Plantilla['IdTerceroPlant'];

                if ($RangoFechas){
                    $sql = $sql . " AND FechaDet >= '" . $RangoFechas[0]. " 00:00:00' AND FechaDet <= '" . $RangoFechas[1] . " 23:59:59'";
                }
                $sql.=" and movimientos_det.Id_Item in (select if(lista_costos_prov_det.Id_Item>0,lista_costos_prov_det.Id_Item,0)  from plantillas_det LEFT JOIN lista_costos_prov_det on lista_costos_prov_det.IdListaCostosProvDet = plantillas_det.IdListaCostosDetPlantDet where plantillas_det.IdPlantilla = ".$Plantilla['IdPlantilla']." )" ;
                $sql.= " group by movimientos_det.Id_Item";
                $var = $sql;
                $arMovDet = DB::select($sql);
                $sql = "UPDATE plantillas_det Set VendidoAnterioridad=0 Where IdPlantilla=" . $Plantilla['IdPlantilla'];
                $command = DB::select($sql);

                foreach ($arMovDet as $MovDet) {
                    $sql = "UPDATE plantillas_det left join lista_costos_prov_det on lista_costos_prov_det.IdListaCostosProvDet = plantillas_det.IdListaCostosDetPlantDet Set VendidoAnterioridad=1 Where IdPlantilla=" . $Plantilla['IdPlantilla'] . " and Id_Item=" . $MovDet->Id_Item;
                    $command = DB::select($sql);
                }
            }
            return [
                'msg'=>"El proceso ha terminado, se actualizaron ".count($arMovDet)." registros !!" ,
                'status'=>201,
            ];
        }
        catch(ErrorException $e){
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage()
            ];
        }
    }

    public function ImportarArchivo(Request $request){
       // if(!$request->ajax()) return  redirect('/');
        $File = $request->file('file');
        $fp = fopen($File, "r");
        $IdPlantilla = $request->Id;
        $Plantilla = Plantillas::find($IdPlantilla);
        $TotalDatos=0;
        $DatosInsert = 0;
        $DatosNoInsert =0;
        $MsgException = null;
        while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
            try{
                $pipe = app(Pipeline::class);
                //Ejecutamos el pipeline que limpiara cada cadena del arreglo.
                $data = $pipe->send($data)->through([
                    QuitarCaracteresString::class,
                    FormatearPrecios::class,
                ])->thenReturn();

                $CodigoG = new PlantillasDet();
                $CodigoG->IdPlantilla = $IdPlantilla;
                $CodigoG->MesesConsumo = $Plantilla->CantidadConsumoMes;
                if (isset($data[0]) && $data[0] != '') {
                    $CodigoG->CodCliente = $data[0];
                } else {
                    $CodigoG->CodCliente = '';
                }
                $CodigoG->Grupo = isset($data[1]) ? $data[1] :'';
                $CodigoG->IdItemCliente = isset($data[2]) ? $data[2]:'';
                if (isset($data[3]) && $data[3] != '') {
                    $CodigoG->DescripcionCliente = $data[3];
                } else {
                    $CodigoG->DescripcionCliente = '';
                }
                if (isset($data[4]) && $data[4] != '') {
                    $CodigoG->MarcaSugerida = $data[4];
                } else {
                    $CodigoG->MarcaSugerida = '';
                }
                if (isset($data[5]) && $data[5] != '') {
                    $CodigoG->UMCliente = $data[5];
                }
                if (isset($data[6]) && is_numeric($data[6])) {
                    $CodigoG->CantidadConsumo = $data[6];
                }
                if (isset($data[7]) && $data[7] != '') {
                    $CodigoG->MarcaAsesor = $data[7];
                }
                if (isset($data[8]) && $data[8] != '') {
                    $CodigoG->ComentariosCliente = $data[8];
                }
                if (isset($data[9]) && is_numeric($data[9])) {
                    $CodigoG->AceptaAlternativa = $data[9];
                }

                if (isset($data[10]) && is_numeric($data[10])) {
                    $CodigoG->PrecioTecho = $data[10];
                }
                if (is_numeric($CodigoG->CantidadConsumo)  && is_numeric($Plantilla->CantidadConsumoMes) > 0) {
                    $CodigoG->CantConsumoMesDet = ($CodigoG->CantidadConsumo / $Plantilla->CantidadConsumoMes);
                }
                $CodigoG->save();
                $DatosInsert++;
            }
            catch(\Illuminate\Database\QueryException  $ex){
                $DatosNoInsert++;
                $MsgException[] = $ex;
            }
            catch(Exception $e){
                $DatosNoInsert++;
                $MsgException[] = $e;
            }
            catch(ErrorException $eex){
                $DatosNoInsert++;
                $MsgException[] = $eex;
            }
            $TotalDatos++;
        }
        

        return [
            'msg'=>"Se leyeron el total de ".$TotalDatos." datos, se registraron ".$DatosInsert." y no se insertaron ".$DatosNoInsert." debido a que pueden tener caracteres especiales o datos incorrectos ",
            'Exceptiones'=>$MsgException,
            'status'=>201
        ];
    }

    public function AplicarCalculoFactor(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $Datos = $request->params['arrItems'];
            $IdPlantilla = $request->params['nIdPlantilla'];
            $FactorNew = $request->params['nFactor'];
            $Cont =0;
            foreach($Datos as $row){
                if($row['Autorizado'] != 1){
                    $Det = PlantillasDet::find($row['IdPlantillaDet']);
                    $DatoAnt = $Det->FactorCliente;
                    $Det->FactorCliente = $FactorNew;
                    $Det->save();
                    $Comentario = "La columna 'FactorCliente'  dato anterior era  = ".$DatoAnt." y el dato nuevo es = ".$Det->FactorCliente;
                    \Funciones::CrearLogPlantillas(16,$IdPlantilla,$Det->IdPlantillaDet,$Comentario,$Det->ItemAba);
                }
                $Cont++;
            }
            \Funciones::ActualizarDatosPlantillaClientes($IdPlantilla);
            DB::commit();
            return [
                'msg'=>"Los detalles seleccionados que no esten autorizados fueron actualizados.",
                'status'=>201,
                'daa'=>$Datos
            ];
        }
        catch(ErrorException $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error",
                'status'=>500,
                'error'=>$e->getMessage(),
            ];
        }
    }
    
    public function ProcesarHomologacion(Request $request) {
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $IdPlantilla = $request->params['IdPlantilla'];
            $Opcion = $request->params['opDetalles'];
            $NroCot = $request->params['nNroCot'];
            $IdPlant = $request->params['nIdPlantilla'];
            $Grupo = $request->params['cGrupo'];
            $oFechasCot = $request->params['oFechasCot'];
            $DetallesSel = $request->params['arrDetallesSel'];
            
            $Plantilla = new Plantillas();
            $Plantilla = Plantillas::find($IdPlantilla);
            $ItemsHM = 0;
            if ($Opcion == 1) {
                $PlantillaDet = PlantillasDet::where('IdPlantilla',$IdPlantilla)->get();
                foreach ($PlantillaDet as $Detalle) {
                    if ($Detalle->IdListaCostosDetPlantDet == null) {
                        if ($NroCot == '0' || $NroCot !='') {
                            $strSql = "SELECT
                                cotizaciones.IdCotizacion,
                                cotizaciones_det.IdCotizacionDet,
                                cotizaciones_det.IdListaCostosDetCot,
                                cotizaciones_det.CodCliente,
                                cotizaciones_det.DescripcionCliente,
                                cotizaciones.NroCotizacion,
                                cotizaciones.IdGrupoCotizacion,
                                cotizaciones.FechaCotizacion
                                FROM
                                cotizaciones
                                INNER JOIN cotizaciones_det ON cotizaciones_det.IdCotizacion = cotizaciones.IdCotizacion
                                WHERE
                                cotizaciones_det.IdListaCostosDetCot IS NOT NULL";

                            if ($NroCot !='0' && $nro) {
                                $strSql = $strSql . " AND cotizaciones.NroCotizacion = " . $NroCot;
                            }
                            if ($Grupo !='') {
                                $strSql = $strSql . " AND cotizaciones.IdGrupoCotizacion = " . $Grupo;
                            }
                            if ($oFechasCot) {
                                $strSql = $strSql . " AND cotizaciones.FechaCotizacion >= '" . $oFechasCot[0] . "' AND cotizaciones.FechaCotizacion <= '" . $oFechasCot[1] . "' AND cotizaciones.IdTerceroCotizacion = " . $Plantilla->IdTerceroPlant;
                            }
                            if ($Detalle->CodCliente != '' || $Detalle->DescripcionCliente != '') {
                                if ($Detalle->CodCliente != '') {
                                    $strSql = $strSql . " AND (cotizaciones_det.CodCliente = '" . $Detalle->CodCliente . "' OR cotizaciones_det.DescripcionCliente = '" . $Detalle->DescripcionCliente . "')" . "AND cotizaciones.IdTerceroCotizacion = " . $Plantilla->IdTerceroPlant;
                                }
                            }
                            $strSql = $strSql . " ORDER BY cotizaciones.NroCotizacion DESC LIMIT 1";
                            $arBuscarDet = DB::select($strSql);
                        } else if ($IdPlant == '0' || $IdPlant !='') {
                            $strSql = "SELECT
                                plantillas.IdPlantilla,
                                plantillas_det.IdPlantillaDet,
                                plantillas_det.IdListaCostosDetPlantDet as IdListaCostosDetCot,
                                plantillas_det.CodCliente,
                                plantillas_det.DescripcionCliente,
                                plantillas_det.Grupo,
                                plantillas.FhPlantilla
                                FROM
                                plantillas
                                INNER JOIN plantillas_det ON plantillas_det.IdPlantilla = plantillas.IdPlantilla
                                WHERE
                                plantillas_det.IdListaCostosDetPlantDet IS NOT NULL";

                        
                            if ($IdPlant != '0' && $IdPlant !='') {
                                $strSql = $strSql . " AND plantillas.IdPlantilla = " . $IdPlant;
                            }
                            if ($Grupo !='') {
                                $strSql = $strSql . " AND plantillas_det.Grupo = '".$Grupo."'";
                            }
                            
                            if ($oFechasCot) {
                                $strSql = $strSql . " AND FhPlantilla >= '" . $oFechasCot[0] . "' AND FhPlantilla <= '" . $oFechasCot[1] . "' AND  plantillas.IdTerceroPlant = " . $Plantilla->IdTerceroPlant;
                            }

                            
                            if ($Detalle->CodCliente != '') {
                                $strSql = $strSql . " AND (plantillas_det.CodCliente = '" . $Detalle->CodCliente . "' OR plantillas_det.DescripcionCliente = '" . $Detalle->DescripcionCliente . "')" . "AND plantillas.IdTerceroPlant  = " . $Plantilla->IdTerceroPlant;                         
                            }

                            $strSql = $strSql . " and plantillas.IdPlantilla != ".$Detalle->IdPlantilla." and  plantillas.Tipo <>1 ORDER BY plantillas.IdPlantilla DESC LIMIT 1";
                            $arBuscarDet = DB::select($strSql);
                        }

                        if (is_countable($arBuscarDet) && count($arBuscarDet) > 0) {
                            $arBuscarDet = $arBuscarDet[0];
                            $DetPlantillaActual = PlantillasDet::find($Detalle->IdPlantillaDet);
                            $ListaCostoDet = ListaCostosProvDet::find($arBuscarDet->IdListaCostosDetCot);
                            $DetPlantillaActual->IdListaCostosDetPlantDet = $arBuscarDet->IdListaCostosDetCot;
                            if ($ListaCostoDet) {
                                $DetPlantillaActual->CantUMMAbaMes = $DetPlantillaActual->CantConsumoMesDet / $ListaCostoDet->FactorCompra;
                                $DetPlantillaActual->SubTotal = $DetPlantillaActual->CantUMMAbaMes * $ListaCostoDet->Costo;
                            }
                            $DetPlantillaActual->save();
                            $ItemsHM++;
                        }
                    }
                }
            }
            else if ($Opcion ==2){
                foreach ($DetallesSel as $Detalle) {
                    if ($Detalle->IdListaCostosDetPlantDet == null) {

                        if ($NroCot !='' || $NroCot == '0') {
                            $strSql = "SELECT
                                cotizaciones.IdCotizacion,
                                cotizaciones_det.IdCotizacionDet,
                                cotizaciones_det.IdListaCostosDetCot,
                                cotizaciones_det.CodCliente,
                                cotizaciones_det.DescripcionCliente,
                                cotizaciones.NroCotizacion,
                                cotizaciones.IdGrupoCotizacion,
                                cotizaciones.FechaCotizacion
                                FROM
                                cotizaciones
                                INNER JOIN cotizaciones_det ON cotizaciones_det.IdCotizacion = cotizaciones.IdCotizacion
                                WHERE
                                cotizaciones_det.IdListaCostosDetCot IS NOT NULL";

                            if ($NroCot !='0' && $NroCot !='') {
                                $strSql = $strSql . " AND cotizaciones.NroCotizacion = " . $NroCot;
                            }
                            if ($Grupo !='') {
                                $strSql = $strSql . " AND cotizaciones.IdGrupoCotizacion = " . $Grupo;
                            }
                            if ($oFechasCot) {
                                $strSql = $strSql . " AND cotizaciones.FechaCotizacion >= '" . $oFechasCot[0] . "' AND cotizaciones.FechaCotizacion <= '" . $oFechasCot[1] . "'
                                                AND cotizaciones.IdTerceroCotizacion = " . $Plantilla->IdTerceroPlant;
                            }
                            if ($Detalle->CodCliente != '' || $Detalle->DescripcionCliente != '') {
                                if ($Detalle->CodCliente != '') {
                                    $strSql = $strSql . " AND (cotizaciones_det.CodCliente = '" . $Detalle->CodCliente . "' OR cotizaciones_det.DescripcionCliente = '" . $Detalle->DescripcionCliente . "')" . "AND cotizaciones.IdTerceroCotizacion = " . $Plantilla->IdTerceroPlant;
                                }
                            }
                            $strSql = $strSql . " ORDER BY cotizaciones.NroCotizacion DESC LIMIT 1";
                            $arBuscarDet = DB::select($strSql);
                        } else if ($IdPlant == '0' || $IdPlant != '') {
                            $strSql = "SELECT
                                plantillas.IdPlantilla,
                                plantillas_det.IdPlantillaDet,
                                plantillas_det.IdListaCostosDetPlantDet as IdListaCostosDetCot,
                                plantillas_det.CodCliente,
                                plantillas_det.DescripcionCliente,
                                plantillas_det.Grupo,
                                plantillas.FhPlantilla
                                FROM
                                plantillas
                                INNER JOIN plantillas_det ON plantillas_det.IdPlantilla = plantillas.IdPlantilla
                                WHERE
                                plantillas_det.IdListaCostosDetPlantDet IS NOT NULL";

                        
                            if ($IdPlant != '0' && $IdPlant !='') {
                                $strSql = $strSql . " AND plantillas.IdPlantilla = " . $IdPlant;
                            }
                            if ($Grupo !='') {
                                $strSql = $strSql . " AND plantillas_det.Grupo = '".$Grupo."'";
                            }
                            
                            if ($oFechasCot) {
                                $strSql = $strSql . " AND FhPlantilla >= '" . $oFechasCot[0] . "' AND FhPlantilla <= '" . $oFechasCot[1] . "' AND  plantillas.IdTerceroPlant = " . $Plantilla->IdTerceroPlant;
                            }

                            
                            if ($Detalle->CodCliente != '') {
                                $strSql = $strSql . " AND (plantillas_det.CodCliente = '" . $Detalle->CodCliente . "' OR plantillas_det.DescripcionCliente = '" . $Detalle->DescripcionCliente . "')" . "AND plantillas.IdTerceroPlant  = " . $Plantilla->IdTerceroPlant;                         
                            }

                            $strSql = $strSql . " and plantillas.IdPlantilla != ".$Detalle->IdPlantilla."  ORDER BY plantillas.IdPlantilla DESC LIMIT 1";
                            $arBuscarDet = DB::select($strSql);
                        }

                        if (is_countable($arBuscarDet) && count($arBuscarDet) > 0) {
                            $DetPlantillaActual = PlantillasDet::find($Detalle->IdPlantillaDet);
                            $ListaCostoDet = ListaCostosProv::find($arBuscarDet->IdListaCostosDetCot);
                            $DetPlantillaActual->IdListaCostosDetPlantDet = $arBuscarDet->IdListaCostosDetCot;
                            if (count($ListaCostoDet) > 0) {
                                $DetPlantillaActual->CantUMMAbaMes = $DetPlantillaActual->CantConsumoMesDet / $ListaCostoDet->FactorCompra;
                                $DetPlantillaActual->SubTotal = $DetPlantillaActual->CantUMMAbaMes * $ListaCostoDet->Costo;
                            }
                            $DetPlantillaActual->save();
                            $ItemsHM++;
                        }
                    }
                }
            }
            DB::commit();
            return [
                'msg'=>"El proceso de homologacion ha sido corrido con exito, se actualizaron ".$ItemsHM,
                'status'=>201,
                'sql'=>$strSql
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>"Ha ocurrido un error intenta nuevamente",
                'status'=>500,
                'error'=>$e->getMessage(),
            ];
        }
    }

    public function AsignarCostoActual(Request $request) {
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $DetallesSel = $request->params['arrDetallesSel'];
            $count = 0;
            $IdPlantilla = $request->params['IdPlantilla'];
            foreach ($DetallesSel as $Det) {
                $arPlantillaDet = PlantillasDet::find($Det['IdPlantillaDet']);
                if ($arPlantillaDet->IdListaCostosDetPlantDet >0 && $arPlantillaDet->Autorizado !=1) {
                    $arDetalleListaCostos = \Funciones::DevEnlaceRaizListaCostos($arPlantillaDet->IdListaCostosDetPlantDet);
                    $arDetalleListaCostos = ListaCostosProvDet::find($arDetalleListaCostos);
                    $arPlantillaDet->IdListaCostosDetPlantDet = $arDetalleListaCostos->IdListaCostosProvDet;
                    if ($arPlantillaDet->CProximo > 0 && $arPlantillaDet->CostoUMMProximo > 0) {
                        $arPlantillaDet->CostoUMMProximo = 0;
                        $arPlantillaDet->CProximo = 0;
                        $arPlantillaDet->CProyectado = 0;
                    }
                    $arPlantillaDet->save();
                    $count = $count + 1;
                }
            }
            if ($count > 0) {
                \Funciones::ActualizarDatosPlantillaClientes($IdPlantilla);
            }
            DB::commit();
            return [
                'msg'=>"Se restablecieron ".$count." costos !!" ,
                'status'=>201,
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return [
                'msg'=>"Upsss ocurrio un error al restablecer los costos " ,
                'status'=>500,
            ];
        }
    }

    public function CrearCotizacion(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try {
            DB::beginTransaction();
            $Parametros = $request->params['FillCrearCot'];
            $ItemsSeleccionados = $request->params['ItemsSeleccionados'];
            $Plantilla = $request->params['Plantilla'];
           
            $Tipo = '';
            $SubTipo='';
            $ValidTipo = $Parametros['valuecot'];
            if($ValidTipo == 'cot-oferta'){
                $Tipo = 4;
                $SubTipo=2;
            }
            else if($ValidTipo == 'cot-preo'){
                $Tipo = 4;
                $SubTipo=1;
            }
            else if($ValidTipo == 'lic-oferta'){
                $Tipo = 2;
                $SubTipo=2;
            }
            else{
                $Tipo = 2;
                $SubTipo=1;
            }
            $CotExist = (int) $Parametros['CotExist'];
            if($CotExist && is_numeric($CotExist) && $CotExist >0){
                $CotizacionNueva = Cotizaciones::find($CotExist);
                if($CotizacionNueva){
                    $MensajeError = [];
                    if($CotizacionNueva->IdTerceroCotizacion != $Plantilla['IdTerceroPlant']){
                        $MensajeError[] = "!no se pueden enviar los datos a la cotizacion por que el tercero ".$CotizacionNueva->IdTerceroCotizacion." es diferente al de la plantilla.";
                    }
                    else if($CotizacionNueva->Estado !='DIGITADA'){
                        $MensajeError[] = "!no se pueden enviar los datos a la cotizacion por que esta ya se encuentra autorizada o cerrada.";
                    }
                }
                else{
                    $MensajeError[] = "!No se econtro una cotizacion con el ID ".$CotExist;
                }

                if(count($MensajeError) >0 ){
                    return[
                        'msg'=>implode('<br>',$MensajeError),
                        'status'=>'500'
                    ];
                }
            }
            else{
                $NmCot = 'Nueva Cotizacion Desde Plantilla : ' . $Plantilla['NmPlantilla'];
                $CotizacionNueva =  \Funciones::CrearCotizacion($Plantilla['IdTerceroPlant'],$Plantilla['IdDireccionPlant'],$Tipo,$SubTipo,$Parametros['perteneceCCto'],$NmCot);
            }
            $ItemsPlantilla = $Parametros['ItemsPlantilla'];
            if($Parametros['OpcionItems'] == 1){
                $ItemsPlantilla = $Parametros['ItemsPlantilla'];
            }
            else{
                $ItemsPlantilla = $ItemsSeleccionados;
            }
            $ValidItemsInsert = false;
            $ConItemsInser =0;
            foreach($ItemsPlantilla as $det){
                $ValidItemsInsert = true;
               
                $CotDetNew = \Funciones::GuardarDetallesCotizacion($CotizacionNueva, $det);
                
                if($CotDetNew){
                    $Det = PlantillasDet::find($det['IdPlantillaDet']);
                    $Det->EnlaceCot = $CotDetNew->IdCotizacionDet;
                    $Det->save();
                    $ConItemsInser++;
                }
            }
            if($ValidItemsInsert && $ConItemsInser >0){
                DB::commit();
                return[
                    'msg'=> is_numeric($CotExist) ? 'Los detalles de enviaron correctamente a la cotización, total items'. $ConItemsInser : '!La cotizacion con ID '.$CotizacionNueva->IdCotizacion.' se ha creado correctamente total items'.$ConItemsInser ,
                    'status'=>'200'
                ];
            }
            else{
                DB::rollBack();
                return[
                    'msg'=>is_numeric($CotExist)? 'No se inserto ningun dato a la cotizacion, valida nuevamente'.$CotExist :'No se pudo crear la cotizacion por que no se insertaron detalles',
                    'status'=>'501'
                ];
            }
           
        } catch (Exception $e) {
            DB::rollBack();
            return[
                'msg'=>'Ocurrio un error al crear la cotización.'.$e->getMessage(),
                'status'=>'501'
            ];
        }
    }

    public function  GuardarFiltro(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try {
            $FiltrosK = $request->params['filtros'];
            $ColumnasK = $request->params['columnas'];
            $FiltrosAct = DB::select("select * from datos_trabajo_plantillas where IdUsuario = '".\Auth::user()->Usuario."'");
            if($FiltrosAct){
                DB::select("update  datos_trabajo_plantillas set FiltrosK2 = '".$FiltrosK."', ColumnasK2 = '".$ColumnasK."'  where IdUsuario = '".\Auth::user()->Usuario."'");
            }
            else{
                DB::select("insert  datos_trabajo_plantillas  (IdUsuario,FiltrosK2,ColumnasK2) values  ('".\Auth::user()->Usuario."','".$FiltrosK."','".$ColumnasK."'");
            }

            return [
                'filtros'=> $FiltrosK,
                'columnas'=> $ColumnasK
            ];
        }
        catch(Exception $e){

        }
    }

    public function ObtenerFiltro(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try {
            $Filtros = DB::select("select FiltrosK2,ColumnasK2 from datos_trabajo_plantillas where IdUsuario = '".\Auth::user()->Usuario."'");
            if($Filtros){
                return [
                    'filtros'=>$Filtros[0]->FiltrosK2,
                    'columnas'=> $Filtros[0]->ColumnasK2
                ];
            }
            else{
                return [
                    'filtros'=>null,
                    'columnas'=> null
                ];
            }
        }
        catch(Exception $e){

        }
    }

    public function CorrerFactoresPlantilla(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try {
            DB::beginTransaction();
            $IdPlantilla = $request['IdPlantilla'];
            $IdPlantillaHm = $request['nIdPlantilla'];
            $OpPlantilla = $request->OpPlantilla;
            $OpItems = $request['OpItems'];
            $ItemsSeleccionados = $request['ItemsSeleccionados'];
            //Validamos si digito un id plantilla
            if($IdPlantillaHm){
                $PlantillaAct = Plantillas::with('tercero','plantillasdet')->where('IdPlantilla',$IdPlantilla)->first();
                $Plantilla = Plantillas::with('tercero','plantillasdet')->where('IdPlantilla',$IdPlantillaHm)->first();
                if($Plantilla && $Plantilla->IdTerceroPlant && $Plantilla->IdTerceroPlant == $PlantillaAct->IdTerceroPlant){
                    if($OpItems == 1){
                        $Cont =0;
                        foreach($PlantillaAct->plantillasdet as $Det){
                            if($Det->IdListaCostosDetPlantDet && $Det->UMCliente && $Det->Autorizado != 1){
                                $DatoFilt = array_filter($Plantilla->plantillasdet->toArray(),function($e) use ($Det){
                                    return $e['IdListaCostosDetPlantDet'] == $Det->IdListaCostosDetPlantDet && $e['FactorCliente']>0  && $e['UMCliente'] == $Det->UMCliente;
                                });
                                if($DatoFilt){
                                    $DatoFilt = reset($DatoFilt);
                                    $Det->FactorCliente = $DatoFilt['FactorCliente'];
                                    $Det->save();
                                    $Cont++;
                                }
                            }
                        }
                        return[
                            'status'=>201,
                            'msg'=>'Se corrieron los factores de '.$Cont.' registros.',
                        ];
                    }
                    else if($ItemsSeleccionados){
                        $Cont =0;
                        foreach($ItemsSeleccionados as $Det){
                            if($Det['IdListaCostosDetPlantDet'] && $Det['UMCliente'] && $Det['Autorizado'] != 1){
                                $DatoFilt = array_filter($Plantilla->plantillasdet->toArray(),function($e) use ($Det){
                                    return $e['IdListaCostosDetPlantDet'] == $Det['IdListaCostosDetPlantDet'] && $e['FactorCliente']>0  && $e['UMCliente'] == $Det['UMCliente'];
                                });
                                if($DatoFilt){
                                    $DatoFilt = reset($DatoFilt);
                                    $Det = PlantillasDet::where('IdPlantillaDet',$Det['IdPlantillaDet'])->first();
                                    $Det->FactorCliente = $DatoFilt['FactorCliente'];
                                    $Det->save();
                                    $Cont++;
                                }
                            }
                        }
                        return[
                            'status'=>201,
                            'msg'=>'Se corrieron los factores de '.$Cont.' registros.',
                        ];
                    }
                }
                else{
                    return[
                        'status'=>201,
                        'msg'=>'No se pueden correr los factores, valida el tercero de la plantilla digitada',
                    ];
                }
            }
            //Validamos si se selecciono desde ultima plantilla
            else if($OpPlantilla == 1){
                $PlantillaAct = Plantillas::with('tercero','plantillasdet')->where('IdPlantilla',$IdPlantilla)->first();
                $Plantilla = Plantillas::with('tercero','plantillasdet')->where('IdTerceroPlant',$PlantillaAct->IdTerceroPlant)->where('Estado','<>','ANULADA')->where('Estado','<>','DIGITADA')->OrderBy('FhPlantilla','DESC')->first();
                if($Plantilla){
                    if($OpItems == 1){
                        $Cont =0;
                        foreach($PlantillaAct->plantillasdet as $Det){
                            if($Det->IdListaCostosDetPlantDet && $Det->UMCliente && $Det->Autorizado != 1){
                                $DatoFilt = array_filter($Plantilla->plantillasdet->toArray(),function($e) use ($Det){
                                    return $e['IdListaCostosDetPlantDet'] == $Det->IdListaCostosDetPlantDet && $e['FactorCliente']>0  && $e['UMCliente'] == $Det->UMCliente;
                                });
                                if($DatoFilt){
                                    $DatoFilt = reset($DatoFilt);
                                    $Det->FactorCliente = $DatoFilt['FactorCliente'];
                                    $Det->save();
                                    $Cont++;
                                }
                            }
                        }
                        return[
                            'status'=>201,
                            'msg'=>'Se corrieron los factores de '.$Cont.' registros.',
                        ];
                    }
                    else if($ItemsSeleccionados){
                        $Cont =0;
                        foreach($ItemsSeleccionados as $Det){
                            if($Det['IdListaCostosDetPlantDet'] && $Det['UMCliente']  && $Det['Autorizado'] != 1){
                                $DatoFilt = array_filter($Plantilla->plantillasdet->toArray(),function($e) use ($Det){
                                    return $e['IdListaCostosDetPlantDet'] == $Det['IdListaCostosDetPlantDet'] && $e['FactorCliente']>0  && $e['UMCliente'] == $Det['UMCliente'];
                                });
                                if($DatoFilt){
                                    $DatoFilt = reset($DatoFilt);
                                    $Det = PlantillasDet::where('IdPlantillaDet',$Det['IdPlantillaDet'])->first();
                                    $Det->FactorCliente = $DatoFilt['FactorCliente'];
                                    $Det->save();
                                    $Cont++;
                                }
                            }
                        }
                        return[
                            'status'=>201,
                            'msg'=>'Se corrieron los factores de '.$Cont.' registros.',
                        ];
                    }
                }
            }
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'status'=>500,
                'msg'=>'Ocurrio un error al correr los factores, valida los datos ingresados e intenta nuevamente.',
            ];
        }
    }

    public function CorrerCostos(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $Datos = \Funciones::ArraryToObject($request->restablecerCostos);
        $ItemsSeleccionados = $request->ItemsSeleccionados;
        $ItemsUpdate =0;
        if($Datos->idListaDesde && $Datos->idListaHasta){
            $DatosListaSel = \Funciones::getDatosPlantillaListaCostos($Datos->IdPlantilla,$Datos->idListaDesde);
            if($DatosListaSel){
                $Items = \Funciones::ArraryToObject($ItemsSeleccionados);
                if($Datos->opItems == 'opTodos'){
                    $Items = PlantillasDet::where('IdPlantilla',$Datos->IdPlantilla)->get();
                }
                foreach($Items as $row){
                    $strSql = "SELECT lista_costos_prov_det.* FROM lista_costos_prov_det WHERE Inactivo = 0 AND IdListaCostosProv=" . $Datos->idListaHasta . " AND IdListaDetReferencia=" . $row->IdListaCostosDetPlantDet;
                    $DatoDet = DB::select($strSql);
                    if($DatoDet && $row->Autorizado != 1){
                        $DatoDet = $DatoDet[0];
                        $PlantillaDet = PlantillasDet::find($row->IdPlantillaDet);
                        $PlantillaDet->IdListaCostosDetPlantDet = $DatoDet->IdListaCostosProvDet;
                        $PlantillaDet->FhDesdeLista = $DatoDet->FhDesde;
                        $PlantillaDet->FhHastaLista = $DatoDet->FhHasta;
                        if ($Datos->costosProximos) {
                            if ($DatoDet->CostoUMMProximo != 0 && $DatoDet->CostoUMMProximo != NULL) {
                                $PlantillaDet->CostoUMMProximo = $DatoDet->CostoUMMProximo;
                                $PlantillaDet->FhDesdeLista = $DatoDet->FhDesdeCostoProx;
                                $PlantillaDet->FhHastaLista = $DatoDet->FhHastaCostoProx;
                                $PlantillaDet->CProximo = 1;
                                $PlantillaDet->CProyectado = $DatoDet->CostoProyectado;
                                $PlantillaDet->save();
                                $ItemsUpdate++;
                            }
                        } else {
                            if ($DatoDet->CostoUMM != 0) {
                                $PlantillaDet->CostoUMMProximo = 0;
                                $PlantillaDet->FhDesdeLista = $DatoDet->FhDesde;
                                $PlantillaDet->FhHastaLista = $DatoDet->FhHasta;
                                $PlantillaDet->CProximo =0;
                                $PlantillaDet->CProyectado = 0;
                                $PlantillaDet->save();
                                $ItemsUpdate++;
                            }
                        }
                    }
                }
            }
        }
        try {
            return[
                'msg'=>'Se actualizaron '.$ItemsUpdate.' detalles.',
                'status'=>200,
                'items_actualizados'=>$ItemsUpdate
            ];
        }
        catch(Exception $e){
            return[
                'msg'=>'Ocurrio un error al correr el proceso.',
                'status'=>500
            ];
        }
    }

    public function AgregarItemsNoHomologados(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $IdPlantilla = $request->params['IdPlantilla'];
        $Fechas = $request->params['oRangoFecha'];
        $Datos = \Funciones::getVentasClientePlantilla($IdPlantilla,$Fechas[0],$Fechas[1]);
        $DatosInsert = 0;
        if($Datos){
            foreach($Datos as $Row){
                $DatoNuevo = new PlantillasDet();
                $DatoNuevo->IdPlantilla = $IdPlantilla;
                if ($Row->CodTercero) {
                    $DatoNuevo->CodCliente = $Row->CodTercero;
                } else {
                    $DatoNuevo->CodCliente = '';
                }
                $DatoNuevo->IdItemCliente = $Row->CodTercero;
                if ($Row->DescripcionTercero) {
                    $DatoNuevo->DescripcionCliente = $Row->DescripcionTercero;
                } else {
                    $DatoNuevo->DescripcionCliente = '';
                }
                if ($Row->UMV) {
                    $DatoNuevo->UMCliente = $Row->UMV;
                }
                $DatoNuevo->IdListaCostosDetPlantDet = $Row->IdListaCostosProvDet;
                $DatoNuevo->ComentariosHM = "No homologados ".\Auth::user()->Usuario." Fechas $Fechas[0]  $Fechas[1]";
                $DatoNuevo->save();
                $DatosInsert++;
            }
        }
        return [
            'msg'=>'Se insertaron '.$DatosInsert." que no estaban homologados en la plantilla.",
            'status'=>201
        ];
    }

    public function Actualizar(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $IdPlantilla = $request->params['IdPlantilla'];
        try{
            \Funciones::ActualizarDatosPlantillaClientes($IdPlantilla);
            return [
                'msg'=>'Se actualizaron los datos',
                'status'=>201
            ];
        }
        catch(Exception $e){
            return [
                'msg'=>'Ocurrio un error al actualizar los datos '.$e->getMessage(),
                'status'=>500
            ];
        }
        
        
    }
}
