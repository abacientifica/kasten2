<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ConfigDocumentos;
use App\Model\Documentos;
use Illuminate\Support\Facades\DB;
use App\Model\ConfigDocumentosDet;

class DocumentosController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()){
            return  redirect('/');
        } 
        $Criterio = $request->criterio;
        $Filtro = $request->buscar;
        if($request->Tp > 0){
            $documentos = Documentos::select('IdDocumento','Nombre','Consecutivo')->where("Tp",$request->Tp);
        }
        else{
            $documentos = Documentos::where('Tp','<>',0);
            if($Criterio !='' && $Filtro !=''){
                $documentos = $documentos->where('Nombre','like','%'.$Filtro.'%');
            }
            $documentos = $documentos->OrderBy('IdDocumento')->get();
        }
        return [
            'documentos'=> $documentos
        ];
    }

    public function CargarCamposconfiguracion(Request $request)
    {
        if(!$request->ajax()){
            return  redirect('/');
        } 

        $IdDoc = $request->IdDoc;
        $Sql ='';
        if(isset($request->Exist)){
            $Sql.=" having(Existe is not null)";
        }
        $StrSql = "select config_documentos.*,
                    (select config_documentos_det.AliasCampo from config_documentos_det where config_documentos_det.IdCampo= config_documentos.IdCampo and  config_documentos_det.IdDoc = 61) as Existe
                    from config_documentos where  1 ";
        if(isset($request->filtro) && $request->filtro !=''){
            $StrSql .=" and  ( config_documentos.IdCampo like '%".$request->filtro."%')";
        }
        $StrSql.= $Sql;
        $Campos = DB::select($StrSql);
        
        return[
            'campos'=>$Campos
        ];
    }

    public function GuardarCampoConfiguracion(Request $request){
        if(!$request->ajax()){
            return  redirect('/');
        } 
        $DatoNew = new ConfigDocumentosDet();
        $DatoNew->IdDoc = $request->IdDoc;
        $DatoNew->IdCampo = $request->IdCampo;
        $DatoNew->AliasCampo = $request->AliasCampo;
        $DatoNew->save();
        return[
            true
        ];
    }

    public function EliminarCampoconfiguracion(Request $request)
    {
        $IdDoc = $request->IdDoc;
        $IdCampo = $request->IdCampo;
        $Campo = ConfigDocumentosDet::where('IdDoc','=',$IdDoc)->where('IdCampo',$IdCampo)->delete();
        
        return[
            true
        ];
    }

    public function ObtenerDocumentosTp(Request $request){
        if(!$request->ajax()){
            return  redirect('/');
        } 
        $Tp = $request->Tp;
        if($Tp > 0){
            if($Tp != 2){
                $documentos = Documentos::select('IdDocumento','Nombre','Consecutivo')->where("Tp",$request->Tp)->get();
            }
            else if($Tp == 2 && \Auth::user()->Tipo != 2){
                $documentos = Documentos::select('IdDocumento','Nombre','Consecutivo')->where("Tp",$request->Tp)->orWhere("IdDocumento",61)->get();
            }
            else if($Tp==2 && \Auth::user()->Tipo == 2){
                $documentos = Documentos::select('IdDocumento','Nombre','Consecutivo')->where("IdDocumento",61)->get();
            }
        }
        else{
            $documentos = Documentos::where('Tp','<>',0);
            if($Criterio !='' && $Filtro !=''){
                $documentos = $documentos->where('Nombre','like','%'.$Filtro.'%');
            }
            $documentos = $documentos->OrderBy('IdDocumento')->get();
        }
        return [
            'documentos'=> $documentos
        ];
    }

}
