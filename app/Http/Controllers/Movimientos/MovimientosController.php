<?php

namespace App\Http\Controllers\Movimientos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Movimientos;
use App\Model\Documentos;
use App\Model\MovimientosDet;
use App\Model\Terceros;

class MovimientosController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()) return redirect("/");

        if($request->nIdDocumento){
            $Movimientos = Movimientos::with('direccion')->where('IdDocumento',$request->nIdDocumento);
            $Filtros = $request;
            if($Filtros->nNroDocumento >0){
                $Movimientos = $Movimientos->where('NroDocumento',$Filtros->nNroDocumento);
            }
            if($Filtros->nIdMovimiento >0){
                $Movimientos = $Movimientos->where('IdMovimiento',$Filtros->nIdMovimiento);
            }
            if($Filtros->nIdTercero >0){
                $Movimientos = $Movimientos->where('IdTercero',$Filtros->nIdTercero);
            }
            if($Filtros->cEstado !=''){
                $Movimientos = $Movimientos->where('Estado',$Filtros->cEstado );
            }
            if($Filtros->nIdDireccion){
                $Movimientos = $Movimientos->where('IdDireccion',$Filtros->nIdDireccion );
            }
            $Movimientos = $Movimientos->limit(100)->orderBy('FhAutoriza','DESC')->get();
            return[
                'movimientos'=>$Movimientos,
            ];
        }
    }

    public function ObtenerMovimiento(Request $request){
        if(!$request->ajax()) return redirect("/");
        $Movimiento = Movimientos::with('documento','asesor','tercero','direccion','fpago')->where('IdMovimiento',$request->IdMov)->get();
        $MovimientosDet = MovimientosDet::with('item')->where('IdMovimiento',$request->IdMov)->get();
        return[
            'movimiento'=>$Movimiento,
            'movimientos_det'=>$MovimientosDet
        ];
    }

    public function RegistrarMovimiento(Request $request){
        if(!$request->ajax()) return redirect("/");
        /*return[
            'movimiento'=>$request->params['fillNuevoMovimiento'],
            'movimientos_det'=>$request->params['arraryDetallesMovimiento']
        ];*/
        try{
            DB::beginTransaction();
            $DatosMovimiento = $request->params['fillNuevoMovimiento'];
            $DatosMovimientoDetalle = $request->params['arraryDetallesMovimiento'];

            $Doc =  Documentos::findOrFail($DatosMovimiento['nIdDocumento']);
            if($DatosMovimiento['nIdTercero'] >0){
                $Tercero = Terceros::findOrFail($DatosMovimiento['nIdTercero']);
            }
            else{
                $Tercero = Terceros::findOrFail(\Auth::user()->IdTercero);
            }
            
            $arMovimiento = new  Movimientos;
            $arMovimiento->IdDocumento = $Doc->IdDocumento;
            $arMovimiento->TpDocumento = $Doc->Tp;
            $arMovimiento->Fecha = date('Y-m-d H:i:s');
            $arMovimiento->Fecha1 = $DatosMovimiento['dFecha1'];
            $arMovimiento->Fecha2 = $DatosMovimiento['dFecha2'];
            $arMovimiento->IdTercero = $DatosMovimiento['nIdTercero'];
            $arMovimiento->IdFormaPago = $DatosMovimiento['nIdFormaPago'];
            $arMovimiento->IdDireccion = $DatosMovimiento['nIdDireccion'];
            $arMovimiento->IdAsesor =  $DatosMovimiento['nIdAsesor'];
            $arMovimiento->Soporte = $DatosMovimiento['cSoporte'];
            $arMovimiento->IdCondEntrega = $DatosMovimiento['nIdCondicionEntrega'];
            $arMovimiento->Estado = 'DIGITADA';
            $arMovimiento->VrOtros = 0;
            $arMovimiento->IdUsuario = \Auth::user()->Usuario;
            $arMovimiento->IdAutoriza = \Auth::user()->Usuario;
            $arMovimiento->FhAutoriza = date("Y-m-d H:i:s");
            $arMovimiento->Prioridad = $DatosMovimiento['nIdPrioridad'];
            $arMovimiento->Comentarios = $DatosMovimiento['cComentarios'];
            $arMovimiento->ComentariosInternos = $DatosMovimiento['cComentariosInt'];
            $arMovimiento->IdTpOc = 1;
            $arMovimiento->save();
            $TotalIvaEnc =0;
            $SubTotalEnc =0;
            $TotalEnc = 0; 
            foreach ( $DatosMovimientoDetalle as  $ep => $det ) {
                $MovimientoDet = new MovimientosDet();
                $MovimientoDet->IdMovimiento = $arMovimiento->IdMovimiento;
                $MovimientoDet->TpDocumento = $arMovimiento->TpDocumento;
                $MovimientoDet->IdDocumento = $arMovimiento->IdDocumento;
                $MovimientoDet->IdTercero = $arMovimiento->IdTercero;
                $MovimientoDet->Id_Item = $det['Id_Item'];
                $MovimientoDet->Cantidad = $det['Cantidad'];
                $MovimientoDet->CantFactor = $MovimientoDet->Cantidad;
                $MovimientoDet->Operacion = $Doc->Operacion;
                $MovimientoDet->Factor = 1;
                $MovimientoDet->PorIva = $det['Iva'];
                $MovimientoDet->Precio = $det['Precio'];
                $MovimientoDet->Estado ='DIGITADO';

                //iva
                $TotalIva = ((($MovimientoDet->Precio - $MovimientoDet->TotalDescuento) * $MovimientoDet->PorIva) / 100) * $MovimientoDet->Cantidad;
                $TotalIvaEnc = $TotalIvaEnc + $TotalIva;
                //subtotal.
                $SubTotal = ($MovimientoDet->Precio * $MovimientoDet->Cantidad) - $MovimientoDet->TotalDescuento;
                $SubTotalEnc = $SubTotalEnc + $SubTotal;
                //total
                $Total = $SubTotal + $TotalIva;
                $TotalEnc = $TotalEnc + $Total;

                $MovimientoDet->TotalIva = $TotalIva;
                $MovimientoDet->SubTotal = $SubTotal;
                $MovimientoDet->Total = $Total;

                $MovimientoDet->save();
            }
            $arMovimiento->Total = $TotalEnc;
            $arMovimiento->VrIva = $TotalIvaEnc;
            $arMovimiento->SubTotal = $SubTotalEnc;
            $arMovimiento->save();
            DB::commit();
            //Autorizamos el documento
            /*$ValidaAut = \Funciones::AutorizarMovimiento($arMovimiento->IdMovimiento);
            \Funciones::Consecutivo($arMovimiento->IdMovimiento);
            

            DB::commit();
            //Enviamos el Email de alerta a el asesor
            $DatosCliente = \Funciones::ObtenerTercero($arMovimiento->IdTercero);
            $strMensaje = "El usuario  " . \Auth::user()->Nombres . " " . \Auth::user()->Apellidos . " de la institución " . $DatosCliente[0]->NombreCorto . " acaba de autorizar el pedido externo " . $arMovimiento->IdMovimiento;
            \Funciones::EnviarEmail('Autorización Pedido Externo','auxsistemas@aba.com.co',$strMensaje);*/
            return [
                'movimiento'=>$arMovimiento->IdMovimiento,
                'msg'=>"El ".$Doc->Nombre." ha sido creado con exito",
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
}
