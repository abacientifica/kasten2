<?php

namespace App\Http\Controllers\Administracion\ConfiguracionDocumentos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\ConfiguracionColumasDocumentos;
use App\Model\ConfiguracionColumasDocumentosDet;

class ConfiguracionColumnasController extends Controller
{
    public function ObtenerConfiguracion(Request $request){
        if(!$request->ajax()) return redirect("/");
        $IdDoc = $request->IdDocumento;
        $Config =  ConfiguracionColumasDocumentos::with('detalles')->where('IdDocumento',$IdDoc)->get();
        if(is_countable($Config) && count($Config)>0){
            return [
                'config'=>$Config[0]
            ];
        }
        else{
            return [
                'config'=>[]
            ];
        }
    }
    public function GuardarConfiguracion(Request $request){
        if(!$request->ajax()) return redirect("/");
        try {
            DB::beginTransaction();
            $IdDoc = $request->params['IdDocumento'];
            $ConfigExist =  ConfiguracionColumasDocumentos::where('IdDocumento',$IdDoc)->first();
            $Orden = 0;
            if(!empty($ConfigExist)){
                $Detalles = ConfiguracionColumasDocumentosDet::where('IdConfiguracion',$ConfigExist->IdConfiguracion)->get();
                if(!empty($Detalles)){
                    foreach($Detalles as $Det){
                        $Det->delete();
                    }
                }
                $DetallesColumas = $request->params['ConfigDet'];
                foreach($DetallesColumas as $det){
                    $ConfigDet = new ConfiguracionColumasDocumentosDet();
                    $ConfigDet->IdConfiguracion = $ConfigExist->IdConfiguracion;
                    $ConfigDet->IdCampo = $det;
                    $ConfigDet->IdOrden = $Orden;
                    $ConfigDet->Usuario = \Auth::user()->Usuario;
                    $ConfigDet->save();
                    $Orden++;
                }
            }
            else{
                $IdDocumento = $request->params['IdDocumento'];
                $ConfigInicial  = $request->params['ConfigInicial'];
                $IdUsuario  = \Auth::user()->Usuario;
                $DetallesColumas = $request->params['ConfigDet'];
                $Config = new ConfiguracionColumasDocumentos();
                $Config->IdDocumento = $IdDocumento;
                $Config->ConfigInicial = $ConfigInicial;
                if($Config->ConfigInicial ==0){
                    $Config->IdUsuario = $IdUsuario;
                }
                $Config->save();
                foreach($DetallesColumas as $det){
                    $ConfigDet = new ConfiguracionColumasDocumentosDet();
                    $ConfigDet->IdConfiguracion = $Config->IdConfiguracion;
                    $ConfigDet->IdCampo = $det;
                    $ConfigDet->IdOrden = $Orden;
                    $ConfigDet->Usuario = \Auth::user()->Usuario;
                    $ConfigDet->save();
                    $Orden++;
                }
            }
            DB::commit();
            return[
                'msg'=>"Configuracion guardada con exito.",
                'status'=>200,
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return[
                'msg'=>'ocurrio un error al guardar los datos '.$th->getMessage(),
                'status'=>200
            ];
        }
    }
}
