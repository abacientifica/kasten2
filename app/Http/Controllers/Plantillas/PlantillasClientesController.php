<?php

namespace App\Http\Controllers\Plantillas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Model\Plantillas;
use App\Model\PlantillasDet;
use App\Model\Cotizaciones;
use App\Model\ListaCostosProvDet;

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
            
            return[
                'plantilla'=>$Plantillas,
                'plantillas_det'=>$PlantillasDet['plantillas_det'],
                'columnas'=>$PlantillasDet['columnas'],
                'filtros'=>$Filtros
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
            $Plantilla->Periodo =  $Datos['dPeriodoAnio']."-".$Datos['dPeriodoMes'];
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
        $PlantDet->IdListaCostosDetPlantDet = $request->params['IdLista'];

        if ($PlantDet->save()) {

            \Funciones::ActualizarDatosPlantillaClientes($PlantDet->IdPlantilla);
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
            }
            \Funciones::ActualizarDatosPlantillaClientes($PlantillaDet->IdPlantilla);
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
        while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
            try{
                $CodigoG = new PlantillasDet();
                $CodigoG->IdPlantilla = $IdPlantilla;
                $CodigoG->MesesConsumo = $Plantilla->CantidadConsumoMes;
                if (isset($data[0]) && $data[0] != '') {
                    $CodigoG->CodCliente = $data[0];
                } else {
                    $CodigoG->CodCliente = '';
                }
                $CodigoG->Grupo = isset($data[1]) ? $this->CambiarCaracteres($data[1]):'';
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
            }
            catch(Exception $e){
                $DatosNoInsert++;
            }
            catch(ErrorException $eex){
                $DatosNoInsert++;
            }
            $TotalDatos++;
        }
        

        return [
            'msg'=>"Se leyeron el total de ".$TotalDatos." datos, se registraron ".$DatosInsert." y no se insertaron ".$DatosNoInsert." debido a que pueden tener caracteres especiales o datos incorrectos",
            'status'=>201
        ];
    }

    public function CambiarCaracteres($cadena) {
        $texto = "";
        if ($cadena != '') {
            $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹", "Ñ","'",'"');
            $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E", "N","","");
            $texto = str_replace($no_permitidas, $permitidas, $cadena);
        }
        return $texto;
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
                        if ($NroCot) {
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

                            if ($NroCot >0) {
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
                        } else if ($IdPlantilla > 0) {
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

                        
                            if ($IdPlant > 0) {
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
                            $ListaCostoDet = ListaCostosProvDet::find($arBuscarDet->IdListaCostosDetCot);
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
            else if ($Opcion ==2){
                foreach ($DetallesSel as $Detalle) {
                    if ($Detalle->IdListaCostosDetPlantDet == null) {

                        if ($NroCot) {
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

                            if ($NroCot >0) {
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
                        } else if ($IdPlant) {
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

                        
                            if ($IdPlant > 0) {
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
            if($CotExist && is_numeric($CotExist)){
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
                    'msg'=>is_numeric($CotExist)? 'No se inserto ningun dato a la cotizacion, valida nuevamente' :'No se pudo crear la cotizacion por que no se insertaron detalles',
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
                DB::select("insert  datos_trabajo_plantillas  (IdUsuario,FiltrosK2,ColumnasK2) values  ('".\Auth::user()->Usuario."','".$request->filtros."','".$request->columnas."'");
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
            return [
                'filtros'=>$Filtros[0]->FiltrosK2,
                'columnas'=> $Filtros[0]->ColumnasK2
            ];
        }
        catch(Exception $e){

        }
    }
}
