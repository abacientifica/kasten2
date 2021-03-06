<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Model\Movimientos;
use App\Model\MovimientosDet;
use App\Model\Direcciones;
use App\Model\Documentos;
use App\Model\Item;
use App\Model\Log;
use App\Model\LogPlantillas;
use App\Model\ListaPreciosDet;
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

    public static function AnularMovimiento($IdMovimiento){
        try{
            DB::beginTransaction();
            $Movimiento = Movimientos::with('documento')->find($IdMovimiento);
            $Movimiento->Estado = "ANULADA";
            $Movimiento->Anulado = 1;
            \Funciones::DevolverCantidades($IdMovimiento, $Movimiento->documento->AfectaCantRef, $Movimiento->documento->Tp);
            MovimientosDet::where('IdMovimiento',$IdMovimiento)->update([
                'Estado'=>'ANULADO'
            ]);

            $Movimiento->VrIva = 0;
            $Movimiento->VrDcto = 0;
            $Movimiento->VrOtros = 0;
            $Movimiento->VrRetencion = 0;
            $Movimiento->SubTotal = 0;
            $Movimiento->VrFletes = 0;
            $Movimiento->VrRteIva = 0;
            $Movimiento->Total = 0;
            $Movimiento->Costo = 0;
            $Movimiento->VrRteFuente = 0;
            $Movimiento->VrRteCree = 0;
            $Movimiento->save();
            //Volver a abrir los documentos enlace
            /*$MovDetLib = MovimientosDetRecord::finder()->FindAllBy_IdMovimiento($IdMovimiento);
            for ($i = 0; $i < count($MovDetLib); $i++) {
                if ($MovDetLib[$i]->Enlace != NULL)
                    MovimientosDetRecord::AbrirAutoDet($MovDetLib[$i]->Enlace);
            }

            for ($i = 0; $i < count($MovDetLib); $i++) {
                if ($MovDetLib[$i]->IdDocumento == 1) {
                    $Item = ItemRecord::finder()->FindByPk($MovDetLib[$i]->Id_Item);
                    $Item->CantOC = $Item->CantOC + $MovDetLib[$i]->Cantidad;
                    $Item->save();
                }
            }*/
            MovimientosDet::where('IdMovimiento',$IdMovimiento)->update([
                'Cantidad'=>0,
                'Total'=>0, 
                'Precio'=>0, 
                'Costo'=>0, 
                'CostoPromedio'=>0, 
                'SubTotal'=>0, 
                'PorIva'=>0, 
                'CantDescuento'=>0, 
                'CantDescuento'=>0, 
                'Enlace'=>NULL
            ]);
            DB::commit();
            return [
                'status'=>200,
                'msg'=>'exito'
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return [
                'status'=>500,
                'msg'=>'Ocurrio un error'.$e->getMessage()
            ];
        }
    }

    public function DesAutorizarMovimiento(Request $request){
        try{
            $Movimiento = Movimientos::findOrFail($IdMovimiento);
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

    public static function DevolverCantidades($IdMovimiento, $AfectaCantRef, $TpDocumento) {
        if ($AfectaCantRef == 1) {
            if ($TpDocumento == 8) {
                $sql = "Select IdMovimientoDet, Id_Item, Cantidad, Enlace,IdDocumento from movimientos_det where Operacion=0 and IdMovimiento=" . $IdMovimiento;
            } else {
                $sql = "Select IdMovimientoDet, Id_Item, Cantidad, Enlace,IdDocumento from movimientos_det where IdMovimiento=" . $IdMovimiento;
            }
            $MovDet = Movimientos::select($sql)->get();
            $TReg = count($MovDet);
            for ($i = 0; $i < $TReg; $i++) {
                if ($MovDet[$i]->Enlace != NULL) {
                    $sql = "UPDATE movimientos_det SET CantAfectada=CantAfectada-" . $MovDet[$i]->Cantidad . " WHERE IdMovimientoDet=" . $MovDet[$i]->Enlace;
                    $rstMovDet = MovimientosDet::select($sql);
                }
            }
        }
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

    public static function DevListaPreciosDet($intIdItem, $intIdTercero, $intIdDireccion,$IdKit =0) {
        if($IdKit =='' || $IdKit == null){
            $IdKit = 0;
        }
        if ($intIdItem != NULL || $intIdItem != "") {
            $arDireccion = Direcciones::findOrFail($intIdDireccion);
            /*$strSql = "SELECT lista_precios_det.*
                   FROM lista_precios_det 
                   LEFT JOIN lista_precios ON lista_precios_det.IdListaPrecios = lista_precios.IdListaPrecios
                   WHERE Inactivo = 0 AND lista_precios_det.IdListaPrecios = " . $arDireccion->IdListaPreciosDireccion . " AND Id_Item = " . $intIdItem . " AND lista_precios.Inactiva = 0 and IdKit=".$IdKit;
            $arListaPreciosDet = DB::select($strSql); //ListaPreciosDetRecord::finder()->FindBySql($strSql);*/
            $arListaPreciosDet = DB::table('lista_precios_det')
            ->leftJoin('lista_precios','lista_precios.IdListaPrecios','=','lista_precios_det.IdListaPrecios')->
            where('Inactivo',0)->
            where('lista_precios_det.IdListaPrecios',$arDireccion->IdListaPreciosDireccion)->
            where('Id_Item',$intIdItem)->where('lista_precios.Inactiva',0)->where('IdKit',$IdKit)->first();
            return $arListaPreciosDet;
        }
        else{
            return [];
        }
    }

    public static function obteneInformacionArchivo($Mime){
        $Row = '';
        $Mime = str_replace('data:','',$Mime);
        switch ($Mime) {
            case $Mime === 'image/png';
                $Row = 'png-image/png';
               
                break;

            case $Mime === 'image/jpeg';
                $Row = 'jpeg-image/jpeg';
               
                break;

            case $Mime === 'image/jpeg';
                $Row = 'jpeg-image/jpeg';
               
                break;

            case $Mime === 'image/gif';
                $Row = 'gif-image/gif';
               
                break;

            case $Mime === 'application/pdf';
                $Row = 'pdf-application/pdf';
               
                break;

            case $Mime === 'application/octet-stream';
                $Row = 'pdf-application/pdf';
               
                break;

            case $Mime === 'application/vnd.ms-office';
                $Row = "xls-application/vnd.ms-excel";
               
                break;
        }

        $Row = explode('-',$Row);
        return [
            'ext'=>$Row[0],
            'tipo'=>'data:'.$Row[1]
        ];
    }

    public static function CargarDetallesPlantillaClientes($IdPlantilla,$IdDoc=83){
        
        $Sql = "select IdPlantillaDet,plantillas_det.CodCliente,plantillas_det.Grupo,plantillas_det.IdItemCliente,plantillas_det.DescripcionCliente,plantillas_det.MarcaSugerida,plantillas_det.UMCliente,plantillas_det.CantidadConsumo,
                plantillas_det.ComentariosCliente
                ,plantillas_det.PrecioTecho,plantillas_det.PrecioSugerido,plantillas_det.MesesConsumo,plantillas_det.CantConsumoMesDet,IF(plantillas_det.AceptaAlternativa = 1,'SI','NO') AS  AceptaAlternativa ,
                plantillas_det.MarcaAsesor, IF(plantillas_det.ReqMuestras = 1,'SI','NO') as ReqMuestras, plantillas_det.CantMuestras ,ComentariosMuestras ,
                terceros.NombreCorto as Prov,item.Id_Item as ItemAba,item.Descripcion as DescripcionAba,lista_costos_prov.NmListaCostos, lista_costos_prov_det.CategoriaPortafolio,NmMarca,lista_costos_prov_det.CodProveedor,lista_costos_prov_det.RefFabricante,
                lista_costos_prov_det.Presentacion,lista_costos_prov_det.UMC,lista_costos_prov_det.UMV,plantillas_det.FactorCliente as FactorCliente,plantillas_det.CantUMMAbaMes,lista_costos_prov_det.CostoUMM,plantillas_det.PrecioTechoUMM,plantillas_det.SubTotalConsumo,
                if(plantillas_det.PrecioTecho >0 , plantillas_det.CantidadConsumo * plantillas_det.PrecioTecho, plantillas_det.CantidadConsumo * lista_costos_prov_det.CostoUMM) as SubTotalVenta,format((plantillas_det.PrecioTecho - lista_costos_prov_det.CostoUMM ) /plantillas_det.PrecioTecho , 2) as UtilVsTecho,'' as FhUltimaFact,'' as ItemContrato,IF(Revisado = 1, 'SI','NO')  as Revisado,
                IF(lista_costos_prov_det.HabCotizar =1 ,'SI','NO') as HabCotizar,if(Autorizado is null,'',if(Autorizado =1,'SI','NO')) as Autorizado,plantillas_det.ComentariosHM,EnlaceCot,IdListaCostosDetPlantDet
                from plantillas_det
                LEFT JOIN lista_costos_prov_det  on lista_costos_prov_det.IdListaCostosProvDet = plantillas_det.IdListaCostosDetPlantDet  
                LEFT JOIN lista_costos_prov_det as ListaDet on ListaDet.IdListaCostosProvDet = lista_costos_prov_det.IdListaDetReferencia
                LEFT JOIN item on item.Id_Item = lista_costos_prov_det.Id_Item
                LEFT JOIN marcas on marcas.IdMarca = lista_costos_prov_det.IdMarca
                LEFT JOIN lista_costos_prov on lista_costos_prov.IdListaCostosProv = lista_costos_prov_det.IdListaCostosProv
                LEFT JOIN lista_costos_prov as ListaProv on ListaProv.IdListaCostosProv = ListaDet.IdListaCostosProv
                LEFT JOIN terceros on terceros.IdTercero = if(lista_costos_prov_det.IdListaDetReferencia is NULL,lista_costos_prov.IdTercero,ListaProv.IdTercero)
                LEFT JOIN terceros as cliente on cliente.IdTercero = plantillas_det.IdTerceroCliente
                where plantillas_det.IdPlantilla =" . $IdPlantilla . "";
        $PlantillasDet = DB::select($Sql);

        $ColumnasConf = DB::select("select * from configuraciones_columnas_documentos_det 
                                    LEFT JOIN configuraciones_columnas_documentos on configuraciones_columnas_documentos.IdConfiguracion = configuraciones_columnas_documentos_det.IdConfiguracion
                                    where IdDocumento =".$IdDoc);

        $Cols=[];
        $a = json_decode(json_encode($PlantillasDet[0]));
        foreach($a as $key => $dat){
            if(count($ColumnasConf)>0){
                foreach($ColumnasConf as $conf){
                    if($conf->IdCampo == $key){
                        $Cols[] = ['columna'=>$key ,'alias'=>$conf->AliasCampo,'pinned'=>$conf->pinned,'ancho'=>$conf->Ancho,'edit'=>$conf->editable];
                        break;
                    }
                }
            }
            /*else{
                $Cols[] =  ['columna'=>$key ,'alias'=>null];
            }*/
        }
        $Cols[] = ['columna'=>'Opciones' ,'alias'=>'HM','pinned'=>'right','edit'=>'false'];
        $Cols[] = ['columna'=>'Eliminar' ,'alias'=>'Elim','pinned'=>'right','edit'=>'false'];
        $Cols[] = ['columna'=>'Editar' ,'alias'=>'Edit','pinned'=>'right','edit'=>'false'];

        return [
            'columnas'=> $Cols,
            'plantillas_det'=>$PlantillasDet,
        ];
    }

    public static function DatosHomologarPlantilla($IdTercero,$Criterios,$Filtros){
        $sql = "SELECT lista_costos_prov_det.*, lista_costos_prov.NmListaCostos,
                      lista_costos_prov.CostosCliente, item.UMM, lista_costos_prov.IdTercero as IdProveedor,
                      (SELECT SUM(Disponible) as Disponible 
                       FROM lotes LEFT JOIN bodegas ON bodegas.IdBodega = lotes.Bodega 
                       WHERE bodegas.SumaDisponible=1 
                       AND lotes.Id_Item=item.Id_Item) as Disponible,
                      item.Descripcion, marcas.NmMarca, 
                      item.EnNovedad,
                      CONCAT(lista_costos_prov_det.FhDesde,' - ',lista_costos_prov_det.FhHasta) as Vigencia, 
                      lista_costos_prov_det.FhHasta
              FROM lista_costos_prov_det 
              left join lista_costos_prov on lista_costos_prov_det.IdListaCostosProv=lista_costos_prov.IdListaCostosProv 
              left join item on lista_costos_prov_det.IdListaCostosProvDet=item.IdListaCostosDetItem
              left join marcas on lista_costos_prov_det.IdMarca=marcas.IdMarca 
              WHERE lista_costos_prov.Inactivo=0 AND lista_costos_prov_det.Id_Item >0 ";
       

        if ($IdTercero) {
            $sql = $sql . " and (lista_costos_prov.CostosCliente = 0 OR lista_costos_prov.IdTercero=" . $IdTercero . ") ";
        }
        if($Criterios && $Filtros){
            $Filtros = explode(',',$Filtros);
            $cont =0;
            $inactivo = false;
            foreach($Criterios as $criterio){
                $Filtro = str_replace(' ','%',$Filtros[$cont]);
                if($criterio == 'lista_costos_prov_det.Inactivo'){
                    $sql.= " and ".$criterio.' = '.$Filtro;
                    $inactivo = true;
                }
                else if(!$inactivo){
                    $sql.= " and lista_costos_prov_det.Inactivo = 0 ";
                    $inactivo = true;
                }
                $sql = $sql ." and  ( ".$criterio. " LIKE '%". $Filtro ."%')" ;
                $cont++;
            }
        }
        $sql.=" limit 100";

        

    

        /*if ($this->ctl0_Main_TxtIdItem->Text != '') {
            $sql = $sql . " and lista_costos_prov_det.Id_Item=" . $this->ctl0_Main_TxtIdItem->Text;
        }

        if ($this->TxtRefProveedor->Text != '') {
            $sql = $sql . " and RefFabricante like '%" . $this->TxtRefProveedor->Text . "%'";
        }

        if ($this->TxtCodProveedor->Text != '') {
            $sql = $sql . " and CodProveedor like '%" . $this->TxtCodProveedor->Text . "%'";
        }

        if ($this->TxtDescripcionProv->Text != '') {
            $sql = $sql . " and DescripcionProv like '%" . $this->TxtDescripcionProv->Text . "%'";
        }

        if ($this->TxtDescripcion->Text != '') {
            $sql = $sql . " and Descripcion like '%" . $this->TxtDescripcion->Text . "%'";
        }

        if ($this->TxtIdTercero->Text != '') {
            $sql = $sql . " and lista_costos_prov.IdTercero = " . $this->TxtIdTercero->Text;
        }
        
        if ($this->ChkDisponible->Checked)
            $sql = $sql . " and (SELECT SUM(Disponible) as Disponible 
                        FROM lotes LEFT JOIN bodegas ON bodegas.IdBodega = lotes.Bodega 
                        WHERE bodegas.SumaDisponible=1 
                        AND lotes.Id_Item=item.Id_Item) > 0";


        $sql = $sql . " order by IdListaCostosProvDet limit 1000";*/

        return $sql;
    }

    public static function ActualizarDatosPlantillaClientes($IdPlantilla){
        $PlantillaDet = new PlantillasDetRecord();
        $PlantillaDet = PlantillasDetRecord::finder()->FindAllBy_AND_IdPlantilla($IdPlantilla);
        $Plantilla = new PlantillasRecord();
        $Plantilla = PlantillasRecord::finder()->FindByPk($IdPlantilla);
        $DatosActualizados = 0;
        if(count($PlantillaDet)>0){
            foreach ($PlantillaDet as $PlantillaDet){
                if($PlantillaDet->IdListaCostosDetPlantDet !=''){
                    $LCdet = ListaCostosProvDetRecord::finder()->FindByPk($PlantillaDet->IdListaCostosDetPlantDet);
                    $Costo =0;
                    if($PlantillaDet->CProximo >0 && $PlantillaDet->CostoUMMProximo >0){
                        $Costo = $PlantillaDet->CostoUMMProximo;
                    }
                    else{
                        $Costo = $LCdet->CostoUMM;
                    }
                    if($PlantillaDet->CantidadConsumo>1 && $PlantillaDet->MesesConsumo>0){
                        $PlantillaDet->CantConsumoMesDet = $PlantillaDet->CantidadConsumo / $PlantillaDet->MesesConsumo;
                    }
                    
                    if($PlantillaDet->FactorCliente >0){
                       $PlantillaDet->CantUMMAbaMes = $PlantillaDet->CantConsumoMesDet / $PlantillaDet->FactorCliente;
                    }
                    else{
                        $PlantillaDet->CantUMMAbaMes =0;
                    }
                    $PlantillaDet->SubTotal = $PlantillaDet->CantUMMAbaMes * $Costo;
                    if($PlantillaDet->FactorCliente >0){
                        $PlantillaDet->SubTotalConsumo = $PlantillaDet->CantidadConsumo / $PlantillaDet->FactorCliente * $Costo;
                    }
                    else{
                        $PlantillaDet->SubTotalConsumo = 0;
                    }
                    //Calcular el precio techo umm en la plantilla no se divida el preciotecho / factor si no que lo multiplique preciotecho * factor 
                    //SOL: Beatriz 17/02/2017
                    if($PlantillaDet->FactorCliente > 0){
                        $PlantillaDet->PrecioTechoUMM = $PlantillaDet->PrecioTecho * $PlantillaDet->FactorCliente;
                    }
                    else{
                        $PlantillaDet->PrecioTechoUMM =0;
                    }
                    $PlantillaDet->save();
                    $DatosActualizados = $DatosActualizados+1;
                    
                }
            }
            if($DatosActualizados > 0){
                $SqlTotal = "select SUM(SubTotal)as SubTotal,SUM(SubTotalConsumo)as SubTotalConsumo from plantillas_det where IdPlantilla =".$IdPlantilla;
                $TotalDet = PlantillasDetRecord::finder()->FindBySql($SqlTotal);
                $sqlSubtotal = "UPDATE plantillas  SET Total=".$TotalDet->SubTotal.",TotalConsumo = ".$TotalDet->SubTotalConsumo." WHERE IdPlantilla =". $IdPlantilla;
                $command = PlantillasRecord::finder()->getDbConnection()->createCommand($sqlSubtotal);
                $command->execute();
            }
        }
        else{
            if($Plantilla->Total >0){
               $Plantilla->Total =0;
            }
            if($Plantilla->TotalConsumo >0){
               $Plantilla->TotalConsumo =0;
            }
            $Plantilla->save();
        }
    }

    public static function CrearLogPlantillas($IdAccion,$IdPlantilla,$IdPlantillaDet,$Comentario =''){
        $Log = new LogPlantillas();
        $Log->Fecha = date('Y-m-d H:i:s');
        $Log->IdAccion = $IdAccion;
        $Log->Usuario = \Auth::user()->Usuario;
        $Log->IdPlantilla = $IdPlantilla;
        $Log->IdPlantillaDet = $IdPlantillaDet;
        $Log->Comentarios = $Comentario;
        $Log->save();
        return true;
    }

}