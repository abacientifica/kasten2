<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Movimientos;
use App\Model\MovimientosDet;
use App\Model\Documentos;
use App\Model\Item;
use App\Model\Log;
use App\Exception\Handler;

class Funciones{

    public static function EnviarEmail($subject,$for,$mensaje){
        try{
            Mail::send('mail.enviarcorreo',['mensaje'=>$mensaje], function($msj) use($subject,$for)
            { 
                $msj->from("kasten@aba.com.co","Kasten V2"); 
                $msj->subject($subject); $msj->to($for); 
            });
            return true;
        }
        catch(\Swift_TransportException $e){
            return $e->getMessage();
        }
    }

    public static function NombreDoc($Id){
        $Documento = Documentos::FindOrFail($Id);
        return $Documento->Nombre;
    }

    public static function ObtenerTercero($IdTercero){
        $tercero = DB::table('terceros')->where('IdTercero','=',$IdTercero)->take(1)->get();
        return $tercero;
    }

    public static function Liquidar($IdMovimiento) {

        $arItem = new ItemRecord();
        $Mov = new Movimientos();
        $Mov = Movimientos::finder()->findByPk($IdMovimiento);
        $Tercero = new TercerosRecord();
        $Tercero = TercerosRecord::finder()->findByPk($Mov->IdTercero);
        $Configuraciones = new ConfiguracionesRecord();
        $Configuraciones = ConfiguracionesRecord::DevConfiguraciones();
        $MovDet = new MovimientosDetRecord();
        $MovDet = MovimientosDetRecord::finder()->findAllBy_IdMovimiento($IdMovimiento);
        $NroReg = count($MovDet);
        $AplicaIvaAnterior = 0;
        if ($Mov->IdContrato != NULL) {
            $Contrato = new ContratosRecord();
            $Contrato = ContratosRecord::finder()->findByPk($Mov->IdContrato);
            if (count($Contrato) > 0) {
                $AplicaIvaAnterior = $Contrato->AplicaIvaAnterior;
            }
        }


        $Precio = false;
        $Doc = DocumentosRecord::finder()->findByPk($Mov->IdDocumento);
        if ($Doc->AfectaPrecios == 1) {
            $Precio = true;
        }



        $i = 0;
        $SubTotalFinal = 0;
        $TotalFinal = 0;
        $DctoFinal = 0;
        $IvaFinal = 0;
        $RteFuente = 0;
        $RteIva = 0;
        $douRteCree = 0;
        $CostoActual = 0;
        while ($i < $NroReg) {

            $Cantidad = $MovDet[$i]->Cantidad;
            $PorDcto = $MovDet[$i]->CantDescuento;

            if ($Precio == true) { // Precio.
                $Costo = $MovDet[$i]->Precio;
                $CostoActual = funciones::CostoActualVigDetalleMov($MovDet[$i]->IdMovimientoDet, 1);
            } else { // Costo.
                $Costo = $MovDet[$i]->Costo;
                $CostoActual = funciones::CostoActualVigDetalleMov($MovDet[$i]->IdMovimientoDet, 2);
                $Dcto = (($Costo * $PorDcto) / 100); // por unidad
                $CostoActual = $Costo - $Dcto;
            }

            //Se redondea para que e subtotal quede tal cual esta con el precio, diego y sandra./26/07/2018
            $Costo = round($Costo, 2);


            if ($Tercero->ExentoIva == 1 && $MovDet[$i]->ExentoIVA == 1) {
                $PorIva = 0;
            } else {
                if ($AplicaIvaAnterior == 0 || $MovDet[$i]->PorIva == 0) {
                    $PorIva = $MovDet[$i]->PorIva;
                } else {
                    $PorIva = $Configuraciones->PorcentajeIvaAnterior;
                }
                if ($PorIva == '') {//Se realiza la validacion para que no saque error en el update.
                    $PorIva = 0;
                }
            }


            //Dcto
            $Dcto = (($Costo * $PorDcto) / 100); // por unidad
            $TotalDcto = $Cantidad * $Dcto;
            $DctoFinal = $DctoFinal + $TotalDcto;

            //iva
            $TotalIva = ((($Costo - $Dcto) * $PorIva) / 100) * $Cantidad;
            $IvaFinal = $IvaFinal + $TotalIva;

            //subtotal.
            $SubTotal = ($Costo * $Cantidad) - $TotalDcto;
            $SubTotalFinal = $SubTotalFinal + $SubTotal;

            //total
            $Total = $SubTotal + $TotalIva;
            $TotalFinal = $TotalFinal + $Total;





            $arItem = ItemRecord::finder()->FindByPk($MovDet[$i]->Id_Item);

            $strSql = "UPDATE movimientos_det SET PorIva= " . ($PorIva) . ", Total=" . ($Total) . ", SubTotal=" . ($SubTotal) . ", TotalIva=" . ($TotalIva) . ", TotalDescuento=" . ($TotalDcto) . ",CostoMvtoVig = if(CostoMvtoVig <=0 OR CostoMvtoVig is Null,'" . $CostoActual . "',CostoMvtoVig) WHERE IdMovimientoDet=" . $MovDet[$i]->IdMovimientoDet;
            $objComando = MovimientosDetRecord::finder()->getDbConnection()->createCommand($strSql);
            $objComando->execute();
            $i = $i + 1;
        }

        $Mov->SubTotal = $SubTotalFinal;
        if ($Mov->IdDocumento == 1 || $Mov->IdDocumento == 21 || $Mov->IdDocumento == 45) {
            if ($Tercero->Autoretenedor == 0) {
                if ($SubTotalFinal >= $Configuraciones->BaseRteFuente)
                    $RteFuente = ($SubTotalFinal * $Configuraciones->PorRteFuente) / 100;
            }

            $Mov->Total = $TotalFinal + $Mov->VrOtros - ($Mov->VrRetencion + $RteFuente);
            $Mov->Costo = $SubTotalFinal;
        } else
            $Mov->Total = $TotalFinal + ($Mov->VrOtros + $Mov->VrFletes);

        //Liquidar la retencion en la fuente de las ventas
        if (($Mov->TpDocumento == 5 && $Tercero->RteFteVenta == 1) || ($Mov->IdDocumento == 8 && $Tercero->RteFteVenta == 1)) {
            if ($Tercero->RteFteVentaSinBase == 1)
                $RteFuente = ($SubTotalFinal * $Configuraciones->PorRteFuente) / 100;
            if ($SubTotalFinal >= $Configuraciones->BaseRteFuente)
                $RteFuente = ($SubTotalFinal * $Configuraciones->PorRteFuente) / 100;
        }

        //Cacular retencion Cree en las facturas //A partir del 2017 se llama auto renta y tiene base
        if ($Mov->TpDocumento == 5 && date('Y-m-d') >= '2013-09-01' && $SubTotalFinal >= $Configuraciones->BaseAutoRenta) {
            $douRteCree = ($SubTotalFinal * $Configuraciones->PorRteCree) / 100;
        }


        //Liquidar retencion de iva para las ventas, solo los grandes contribuyentes y entidades del estado nos retienen 50% iva
        if ($Mov->TpDocumento == 5 && ($Tercero->IdClasificacionTributaria == 5 || $Tercero->IdClasificacionTributaria == 3)) {
            //Validacion acordada con Luz Dary de que las devoluciones tambien validen la base
            if ($IvaFinal >= $Configuraciones->BaseRteIvaVentas) {
                $RteIva = ($IvaFinal * $Configuraciones->PorRteIva) / 100;
            }
        }


        $Mov->VrDcto = $DctoFinal;
        $Mov->VrIva = $IvaFinal;
        $Mov->VrRteFuente = $RteFuente;
        $Mov->VrRteIva = $RteIva;
        $Mov->VrRteCree = $douRteCree;
        $Mov->save();
        funciones::ValidarAfectadasMov($IdMovimiento);
        funciones::ValidarDatosInvimaProducto($IdMovimiento);
        //Liquidar el total de las entradas de almacen por compras por si tiene retenciones por descuentos financieros
        if ($Mov->IdDocumento == 1) {
            funciones::LiquidarOtrosEA($IdMovimiento);
        }
    }


    public static function AutorizarMovimiento($IdMovimiento) {
        try{
            $Movimientos = Movimientos::findOrFail($IdMovimiento);
            $Movimientos->Estado = "AUTORIZADA";
            $Movimientos->Autorizado = 1;
            $Movimientos->IdAutoriza = \Auth::user()->Usuario;
            $Movimientos->FhAutoriza = date('y-m-d H:i:s');
            //Para que las facturas sin imprimir queden con la fecha de autorizacion
            if ($Movimientos->IdDocumento == 3)
                $Movimientos->Fecha = $Movimientos->FhAutoriza;

            if ($Movimientos->documento->Operacion == 1) {
                $Movimientos->FhAutoriza = $Movimientos->Fecha;
                $FechaDocEtrada = $Movimientos->Fecha;
            } else{
                $FechaDocEtrada = date('Y-m-d H:i:s');
            }
            $Movimientos->save();
            foreach($Movimientos->detalles as $det){
                $MovDet = MovimientosDet::findOrFail($det->IdMovimientoDet);
                $MovDet->Estado='AUTORIZADO';
                $MovDet->save();
            }

            DB::update("UPDATE movimientos_det SET FechaDet='" . $FechaDocEtrada . "' WHERE IdMovimiento=" . $IdMovimiento);
            \Funciones::CrearLog(2, $Movimientos->IdMovimiento, \Auth::user()->Usuario);
            return true;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function DesAutorizarMovimiento(Request $request){
        try{
            $Movimientos = Movimientos::findOrFail($IdMovimiento);
            $Movimientos->Estado = "DIGITADA";
            $Movimientos->Autorizado = 1;
            $Movimientos->IdAutoriza = \Auth::user()->Usuario;
            $Movimientos->FhAutoriza = date('y-m-d H:i:s');
            //Para que las facturas sin imprimir queden con la fecha de autorizacion
            if ($Movimientos->IdDocumento == 3)
                $Movimientos->Fecha = $Movimientos->FhAutoriza;

            if ($Movimientos->documento->Operacion == 1) {
                $Movimientos->FhAutoriza = $Movimientos->Fecha;
                $FechaDocEtrada = $Movimientos->Fecha;
            } else{
                $FechaDocEtrada = date('Y-m-d H:i:s');
            }
            $Movimientos->save();
            foreach($Movimientos->detalles as $det){
                $MovDet = MovimientosDet::findOrFail($det->IdMovimientoDet);
                $MovDet->Estado='DIGITADO';
                $MovDet->save();
            }
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public static function Consecutivo($IdMov) {
        try{
            $Movimiento = Movimientos::findOrFail($IdMov);
            if ($Movimiento->NroDocumento == "" || $Movimiento->NroDocumento == 0){
                $Movimiento->NroDocumento = \Funciones::SacarConsecutivo($Movimiento->IdDocumento);
            }

            if (($Movimiento->IdDocumento == 3 || $Movimiento->IdDocumento == 11 || $Movimiento->TpDocumento == 6 || $Movimiento->TpDocumento == 10 || $Movimiento->documento->Operacion == 0) && $Movimiento->TpDocumento != 8) {
                $Movimiento->Fecha = date('Y-m-d H:i:s');
                $Movimiento->FhAutoriza = date('Y-m-d H:i:s');
                DB::update("UPDATE movimientos_det SET FechaDet='" . date('Y-m-d H:i:s') . "', NroDocumento=" . $Movimiento->NroDocumento . " WHERE IdMovimiento=" . $IdMov);
                
            }

            if ($Movimiento->IdDocumento == 3 || $Movimiento->IdDocumento == 11){
                $Movimiento->Fecha2 = date('Y-m-d', (strtotime(TercerosRecord::PlazoPago($Movimiento->IdTercero) . " days"))); // Fecha de vencimiento.
            }

            $Movimiento->save();
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    public static function SacarConsecutivo($IdDocumento) {
        $Documentos = Documentos::findOrFail($IdDocumento);
        $Consecutivo = $Documentos->Consecutivo;
        $Documentos->Consecutivo = $Documentos->Consecutivo + 1;
        $Documentos->save();
        return $Consecutivo;
    }

    public static function CrearLog($IdAccion, $IdMovimiento, $strUsuario) {
        $LogNew = new Log;
        $LogNew->IdAccion = $IdAccion;
        $LogNew->IdMovimiento = $IdMovimiento;
        $LogNew->Usuario = $strUsuario;
        $LogNew->Fecha = date('y-m-d H:i:s');
        $LogNew->save();
    }

    function   mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}