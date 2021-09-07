<?php

namespace App\Http\Controllers\Movimientos;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Movimientos;
use App\Model\Documentos;
use App\Model\MovimientosDet;
use App\Model\Terceros;
use App\Model\RevisionDoc;
use App\Model\Item;
use App\Model\ListaPreciosDet;

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
            if($Filtros->oRangoFechas){
                $Movimientos = $Movimientos->whereBetween('FhAutoriza',[$Filtros->oRangoFechas[0],$Filtros->oRangoFechas[1]]);
            }
            $Movimientos = $Movimientos->where('IdDireccion','<>','');
            
            $Movimientos = $Movimientos->limit(100)->orderBy('FhAutoriza','DESC')->get();
            return[
                'movimientos'=>$Movimientos
            ];
        }
    }

    public function ObtenerMovimiento(Request $request){
        if(!$request->ajax()) return redirect("/");
        $Movimiento = Movimientos::with('documento','asesor','tercero','tercero.asesorservcliente','direccion','fpago','condentrega')->where('IdMovimiento',$request->IdMov)->where('IdDocumento',$request->IdDoc);
        if(\Auth::user()->Tipo == 2){
            $Movimiento = $Movimiento->where('IdTercero',\Auth::user()->IdTercero);
        }
        $Movimiento = $Movimiento->get();
        $MovimientosDet = MovimientosDet::with('item','item.listacostosdet','item.listacostosdet.marca')->where('IdMovimiento',$request->IdMov)->where('IdDocumento',$request->IdDoc)->get();
        if(is_countable($Movimiento) && count($Movimiento)>0){
            return[
                'movimiento'=>$Movimiento,
                'movimientos_det'=>$MovimientosDet,
                'msg'=>"encontrado"
            ];
        }
        else{
            return[
                'movimiento'=>$Movimiento,
                'movimientos_det'=>$MovimientosDet,
                'msg'=>"no_encontrado"
            ];
        }
    }

    public function RegistrarMovimiento(Request $request){
        if(!$request->ajax()) return redirect("/");
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
                $Item = Item::findOrFail($det['Id_Item']);
                $MovimientoDet->IdMovimiento = $arMovimiento->IdMovimiento;
                $MovimientoDet->TpDocumento = $arMovimiento->TpDocumento;
                $MovimientoDet->IdDocumento = $arMovimiento->IdDocumento;
                $MovimientoDet->IdTercero = $arMovimiento->IdTercero;
                $MovimientoDet->Id_Item = $det['Id_Item'];
                $MovimientoDet->Cantidad = $det['Cantidad'];
                $MovimientoDet->CantFactor = $MovimientoDet->Cantidad;
                $MovimientoDet->Operacion = $Doc->Operacion;
                $MovimientoDet->IdLista = $det['IdLista']; 
                $MovimientoDet->Factor = 1;
                $MovimientoDet->PorIva = $det['Iva'];
                $MovimientoDet->Precio = $det['Precio'];
                $MovimientoDet->Estado ='DIGITADO';
                $MovimientoDet->UM = $Item->UMM;

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
            Movimientos::where('IdMovimiento',$arMovimiento->IdMovimiento)->update(['Total' => $TotalEnc,'VrIva'=>$TotalIvaEnc,'SubTotal'=>$SubTotalEnc]);
            DB::commit();
            //Autorizamos el documento
            /*$ValidaAut = \Funciones::AutorizarMovimiento($arMovimiento->IdMovimiento);
            \Funciones::Consecutivo($arMovimiento->IdMovimiento);
            

            DB::commit();
            //Enviamos el Email de alerta a el asesor
            $DatosCliente = \Funciones::ObtenerTercero($arMovimiento->IdTercero);
            $strMensaje = "El usuario  " . \Auth::user()->Nombres . " " . \Auth::user()->Apellidos . " de la institución " . $DatosCliente[0]->NombreCorto . " acaba de autorizar el pedido externo " . $arMovimiento->IdMovimiento;
            \Funciones::EnviarEmail('Autorización Pedido Externo','auxsistemas@aba.com.co',$strMensaje);*/
            \Funciones::CrearLog(8, $arMovimiento->IdMovimiento, \Auth::user()->Usuario);
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

    public function ActualizarMovimiento(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            DB::beginTransaction();
            $nIdMovimiento = $request->params['nIdMovimiento'];
            $DatosMovimientoDetalle = $request->params['arraryDetallesMovimiento'];

            $arMovimiento = Movimientos::FindOrFail($nIdMovimiento);
            $Doc =  Documentos::find($arMovimiento->IdDocumento);
            $TotalIvaEnc =0;
            $SubTotalEnc =0;
            $TotalEnc = 0; 
            foreach ( $DatosMovimientoDetalle as  $ep => $det ) {
                $MovimientoDet = MovimientosDet::find($det['IdMovimientoDet']);
                $MovimientoDet->Cantidad = $det['Cantidad'];
                $MovimientoDet->CantFactor = $MovimientoDet->Cantidad;
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
                'msg'=>"El ".$Doc->Nombre." ha sido actualizado con exito",
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

    public function Autorizar(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            $MovAutorizado = \Funciones::AutorizarMovimiento($request->params['nIdMovimiento']);
            $arMovimiento = Movimientos::with('tercero','tercero.asesorservcliente')->find($request->params['nIdMovimiento']);
            $EmailAsesor = $arMovimiento->tercero->asesorservcliente->Email;
            $EmailAsesor = !filter_var($EmailAsesor,FILTER_VALIDATE_EMAIL) ? 'auxsistemas@aba.com.co' : $EmailAsesor;
            if($MovAutorizado == true){
                \Funciones::Consecutivo($request->params['nIdMovimiento']);
                $Mov =Movimientos::with('documento')->find($request->params['nIdMovimiento']);
                //Enviamos el Email de alerta a el asesor
                $DatosCliente = \Funciones::ObtenerTercero($arMovimiento->IdTercero);
                $strMensaje = "El usuario  " . \Auth::user()->Nombres . " " . \Auth::user()->Apellidos . " de la institución " . $DatosCliente[0]->NombreCorto . " acaba de autorizar el pedido externo " . $Mov->NroDocumento;
                //Se comenta el envio de email
                return[
                    'msg'=>"El movimiento ha sido autorizado con exito !!",
                    'status'=>201,
                    'Email'=>\Funciones::EnviarEmail('Autorización Pedido Externo',$EmailAsesor,$strMensaje)
                ];
            }
            else{
                return[
                    'msg'=>$MovAutorizado,
                    'status'=>501
                ];
            }
        }
        catch(Exception $e){
            return[
                'msg'=>"Ups.. ocurrio un error al autorizr el movimiento :".$e->getMessage(),
                'status'=>501
            ];
        }
    }

    public function anularMovimiento(Request $request){
        try {
            DB::beginTransaction();
            $Mov = Movimientos::with('documento')->Find($request->nIdMov);
            if ($Mov->Importado == 0 && $Mov->Contabilizado == 0) {
                $MovAnulado = \Funciones::anularMovimiento($request->nIdMov);
                if ($MovAnulado['status'] == 200) {
                    $Anulaciones = new RevisionDoc();
                    $Anulaciones->Tipo = 1;
                    $Anulaciones->IdConcepto = 13;
                    $Anulaciones->IdMovimiento = $request->nIdMov;
                    $Anulaciones->FhRevision = date('Y-m-d H:i:s');
                    $Anulaciones->IdUsuario = \Auth::user()->Usuario;
                    $Anulaciones->Comentarios = $request->Comentarios;
                    $Anulaciones->save();
                    \Funciones::CrearLog(1, $Mov->IdMovimiento, \Auth::user()->Usuario);
                }
            } 
            DB::commit();
            return [
                'status'=>200,
                'msg'=>$Mov->documento->Nombre." #".$Mov->NroDocumento.", anulado con exito !!!"
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status'=>500,
                'msg'=>'Ocurrio un error'.$e->getMessage()
            ];
        }
    }

    public function NotificarMovimiento(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            $arMovimiento = Movimientos::find($request->params['nIdMovimiento']);
            $DatosCliente = \Funciones::ObtenerTercero($arMovimiento->IdTercero);
            $strMensaje = "El usuario  " . \Auth::user()->Nombres . " " . \Auth::user()->Apellidos . " de la institución " . $DatosCliente[0]->NombreCorto . " acaba de autorizar el pedido externo " . $arMovimiento->IdMovimiento;
            $Mensaje = \Funciones::EnviarEmail('Autorización Pedido Externo','auxsistemas@aba.com.co',$strMensaje);
            return[
                'msg'=>"Ups, el area de servicio al cliente fue notificada con exito ",
                'status'=>201,
                'mensaje'=>$Mensaje
            ];
        }
        catch(Exception $e){
            return[
                'msg'=>$e,
                'status'=>200
            ];
        }

    }

    public function EliminarMovimientoDet(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            DB::beginTransaction();
            $IdMovDet = $request->params['nIdMovimientoDet'];
            $MovimientoDet = MovimientosDet::with('item')->find($IdMovDet);
            $DetElim = $MovimientoDet->item->Descripcion. " COD ".$MovimientoDet->Id_Item;
            $Movimiento =  Movimientos::find($MovimientoDet->IdMovimiento);
            $MovimientoDet->delete();
            $MovDets = MovimientosDet::where('IdMovimiento',$Movimiento->IdMovimiento)->get();
            if(is_countable($MovDets) && count($MovDets)>0){
                $TotalIvaEnc =0;
                $SubTotalEnc =0;
                $TotalEnc = 0; 
                foreach($MovDets as $det){
                    //iva
                    $TotalIva = ((($det->Precio - $det->TotalDescuento) * $det->PorIva) / 100) * $det->Cantidad;
                    $TotalIvaEnc = $TotalIvaEnc + $TotalIva;
                    //subtotal.
                    $SubTotal = ($det->Precio * $det->Cantidad) - $det->TotalDescuento;
                    $SubTotalEnc = $SubTotalEnc + $SubTotal;
                    //total
                    $Total = $SubTotal + $TotalIva;
                    $TotalEnc = $TotalEnc + $Total;
                }
                $Movimiento->Total = $TotalEnc;
                $Movimiento->VrIva = $TotalIvaEnc;
                $Movimiento->SubTotal = $SubTotalEnc;
                $Movimiento->save();
                
            }
            DB::commit();
            return[
                'msg'=>"Se ha eliminado el ".$DetElim,
                'status'=>201
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>$e,
                'status'=>500
            ];
        }
    }

    public function AgregarProducto(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            DB::beginTransaction();
            $nIdMovimiento = $request->params['nIdMovimiento'];
            $DatosMovimientoDetalle = $request->params['arraryDetalleMovimiento'];

            $arMovimiento = Movimientos::FindOrFail($nIdMovimiento);
            $Doc =  Documentos::find($arMovimiento->IdDocumento);
            $TotalIvaEnc =0;
            $SubTotalEnc =0;
            $TotalEnc = 0; 
            foreach ( $DatosMovimientoDetalle as  $ep => $det ) {
                $MovimientoDet = new MovimientosDet();
                $Item = Item::findOrFail($det['Id_Item']);
                $MovimientoDet->IdMovimiento = $arMovimiento->IdMovimiento;
                $MovimientoDet->TpDocumento = $arMovimiento->TpDocumento;
                $MovimientoDet->IdDocumento = $arMovimiento->IdDocumento;
                $MovimientoDet->IdTercero = $arMovimiento->IdTercero;
                $MovimientoDet->Id_Item = $det['Id_Item'];
                $MovimientoDet->Cantidad = $det['Cantidad'];
                $MovimientoDet->CantFactor = $MovimientoDet->Cantidad;
                $MovimientoDet->Operacion = $Doc->Operacion;
                $MovimientoDet->IdLista = $det['IdLista']; ;
                $MovimientoDet->Factor = 1;
                $MovimientoDet->PorIva = $det['Iva'];
                $MovimientoDet->Precio = $det['Precio'];
                $MovimientoDet->Estado ='DIGITADO';
                $MovimientoDet->UM = $Item->UMM;
                $MovimientoDet->save();
            }
            $MovDets = MovimientosDet::where('IdMovimiento',$arMovimiento->IdMovimiento)->get();
            foreach ($MovDets as $det ) {
                $MovimientoDet = MovimientosDet::find($det['IdMovimientoDet']);
                $MovimientoDet->Cantidad = $det['Cantidad'];
                $MovimientoDet->CantFactor = $MovimientoDet->Cantidad;
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
            return[
                'msg'=>"El producto fue agregado correctamente",
                'status'=>200,
            ];

        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>$e,
                'status'=>500
            ];
        }
    }

    public function setGenerarDocumento(Request $request){
        $Movimiento = Movimientos::with('documento','asesor','tercero','tercero.asesorservcliente','direccion','fpago','condentrega')->where('IdMovimiento',$request->nIdMov)->where('IdDocumento',$request->nIdDoc)->get();
        $MovimientosDet = MovimientosDet::with('item','item.listacostosdet','item.listacostosdet.marca')->where('IdMovimiento',$request->nIdMov)->where('IdDocumento',$request->nIdDoc)->get();
        $pdf = PDF::loadView('pdf.pedidos.ver',[
            'movimiento'=>$Movimiento,
            'movimientos_det'=>$MovimientosDet
        ]);
        \Funciones::CrearLog(7, $Movimiento[0]->IdMovimiento, \Auth::user()->Usuario);
        return $pdf->download('invoice.pdf');
        /*return [
            'pdf'=>$request->nIdMovimiento
        ];*/
    }
}
