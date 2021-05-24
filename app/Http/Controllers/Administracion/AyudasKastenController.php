<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AyudasKasten;
use App\Model\AyudasKastenDet;
use Illuminate\Support\Facades\DB;
use App\Model\AyudasKastenDetItem;
use Illuminate\Support\Facades\Storage;
class AyudasKastenController extends Controller
{
    public function index(Request $request){
        try {
            $ayudas = AyudasKasten::select('ayudas_kasten.id','ayudas_kasten.Nombre','IdDoc','IdSlug','Usuario','documentos.Nombre as NmDoc','permisos.nombre as Url')->
            leftjoin('documentos','documentos.IdDocumento','ayudas_kasten.IdDoc')->
            leftjoin('permisos','permisos.id','ayudas_kasten.IdSlug')->
            get();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayudas'=>$ayudas
            ];
        } catch (Exception $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la consulta" .$e->getMessage(),
                'ayudas'=>[]
            ];
        }
    }

    public function registrarAyuda(Request $request){
        try {
            $ayuda = new AyudasKasten();
            $ayuda->Nombre = $request->params['NmAyuda'];
            $ayuda->IdDoc =  $request->params['IdDoc'];
            $ayuda->IdSlug =  $request->params['IdSlug'];
            $ayuda->Usuario = \Auth::user()->Usuario;
            $ayuda->save();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }

    public function registrarAyudaDet(Request $request){
        try {
            $ayuda = new AyudasKastenDet();
            $ayuda->IdAyuda = $request->params['IdAyuda'];
            $ayuda->TituloAyuda = $request->params['TituloAyuda'];
            $ayuda->Descripcion = $request->params['Descripcion'];
            $request->params['Imagen']; 
            if(isset($request->params['Imagen']) && $request->params['Imagen']!=''){
                $imageInfo = explode(";base64,", $request->params['Imagen']); 
                $DatosArchivo = \Funciones::obteneInformacionArchivo($imageInfo[0]);
                $image = str_replace(' ', '+', $imageInfo[1]); 
                $imageName = "ayuda-".date("ymdhis").".".$DatosArchivo['ext']; 
                Storage::disk('ayudas')->put($imageName, base64_decode($image));
                $ayuda->Imagen  = '/storage/ayudas/'.$imageName;
            }
            $ayuda->Usuario = \Auth::user()->Usuario;
            $ayuda->save();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
        
    }

    public function registrarAyudaItem(Request $request){
        try {
            $ayuda = new AyudasKastenDetItem();
            $ayuda->IdAyudaDet = $request->fillCrearAyudaDet['IdAyudaDet'];
            $ayuda->Titulo = $request->fillCrearAyudaDet['Titulo'];
            $ayuda->Descripcion = $request->fillCrearAyudaDet['Descripcion'];
            $ayuda->Posicion = $request->fillCrearAyudaDet['Posicion'];
            
            if(isset($request->fillCrearAyudaDet['Imagen']) && $request->fillCrearAyudaDet['Imagen']!=''){
                $imageInfo = explode(";base64,", $request->fillCrearAyudaDet['Imagen']); 
                $DatosArchivo = \Funciones::obteneInformacionArchivo($imageInfo[0]);
                $image = str_replace(' ', '+', $imageInfo[1]); 
                $imageName = "ayuda-".date("ymdhis").".".$DatosArchivo['ext']; 
                Storage::disk('ayudas')->put($imageName, base64_decode($image));
                $ayuda->Imagen  = '/storage/ayudas/'.$imageName;
            }
            $ayuda->Usuario = \Auth::user()->Usuario;
            $ayuda->save();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }
        
    public function ObtenerAyudaDet(Request $request){
        try {
            $ayudasdet = AyudasKastenDet::findOrFail($request->id);
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayudadet'=>$ayudasdet
            ];
        } catch (Exception $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la consulta" .$e->getMessage(),
                'ayudadet'=>[]
            ];
        }
    }

    public function ObtenerAyudas(Request $request){
        try {
            $IdAyuda = $request->id;
            $ayudasdet = AyudasKastenDet::where('IdAyuda',$IdAyuda)->get();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa".$IdAyuda."entro",
                'ayudasdet'=>$ayudasdet
            ];
        } catch (Exception $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la consulta" .$e->getMessage(),
                'ayudasdet'=>[]
            ];
        }
    }

    public function ObtenerAyudasItems(Request $request){
        try {
            $ayudasitems = AyudasKastenDetItem::all();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayudasitem'=>$ayudasitems
            ];
        } catch (Exception $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la consulta" .$e->getMessage(),
                'ayudasitem'=>[]
            ];
        }
    }

    public function ActualizarAyuda(Request $request){
        try {
            $ayuda = AyudasKasten::find($request->params['id']);
            $ayuda->Nombre = $request->params['NmAyuda'];
            $ayuda->IdDoc =  $request->params['IdDoc'];
            $ayuda->IdSlug =  $request->params['IdSlug'];
            $ayuda->save();
            return[
                'status'=>201,
                'msg'=> "ayuda actualizada exitosamente",
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }

    public function ActualizarAyudaDet(Request $request){
        try {
            $ayuda =  AyudasKastenDet::findOrFail($request->params['id']);
            $ayuda->TituloAyuda = $request->params['TituloAyuda'];
            $ayuda->Descripcion = $request->params['Descripcion'];
            $Elimino = false;
            if(isset($request->params['Imagen']) && $request->params['Imagen']!=''){
                if($ayuda->Imagen != null || $ayuda->Imagen != ''){
                    Storage::delete($ayuda->Imagen);
                    $Elimino = true;
                }
                $imageInfo = explode(";base64,", $request->params['Imagen']); 
                $DatosArchivo = \Funciones::obteneInformacionArchivo($imageInfo[0]);
                $image = str_replace(' ', '+', $imageInfo[1]); 
                $imageName = "ayuda-".date("ymdhis").".".$DatosArchivo['ext']; 
                Storage::disk('ayudas')->put($imageName, base64_decode($image));
                $ayuda->Imagen  = '/storage/ayudas/'.$imageName;
            }
            $ayuda->Usuario = \Auth::user()->Usuario;
            $ayuda->save();
            return[
                'status'=>201,
                'msg'=> "Actualización exitosa ".$Elimino,
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }

    public function ActualizarAyudaDetItem(Request $request){
        try {
            $ayuda =  AyudasKastenDetItem::findOrFail($request->id);
            $ayuda->IdAyudaDet = $request->fillCrearAyudaDet['IdAyudaDet'];
            $ayuda->Titulo = $request->fillCrearAyudaDet['Titulo'];
            $ayuda->Descripcion = $request->fillCrearAyudaDet['Descripcion'];
            $ayuda->Posicion = $request->fillCrearAyudaDet['Posicion'];
            
            if(isset($request->fillCrearAyudaDet['Imagen']) && $request->fillCrearAyudaDet['Imagen']!=''){
                $imageInfo = explode(";base64,", $request->fillCrearAyudaDet['Imagen']); 
                $DatosArchivo = \Funciones::obteneInformacionArchivo($imageInfo[0]);
                $image = str_replace(' ', '+', $imageInfo[1]); 
                $imageName = "ayuda-".date("ymdhis").".".$DatosArchivo['ext']; 
                Storage::disk('ayudas')->put($imageName, base64_decode($image));
                $ayuda->Imagen  = '/storage/ayudas/'.$imageName;
            }
            $ayuda->Usuario = \Auth::user()->Usuario;
            $ayuda->save();
            return[
                'status'=>201,
                'msg'=> "consulta exitosa",
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }

    public function EliminarAyuda(Request $request){
        if(!$request->ajax()) return redirect("/");
        try {
            DB::beginTransaction();
            $Eliminada = false;
            $ayuda = AyudasKasten::find($request->params['id']);
            $ayudasdet =  AyudasKastenDet::where("IdAyuda",$ayuda->id)->get();
            if(is_countable($ayudasdet) && count($ayudasdet)==0){
                $ayuda->delete();
                $Eliminada = true;
            }
            DB::commit();
            return[
                'status'=>$Eliminada ? 201 : 500,
                'msg'=> $Eliminada ? "La ayuda fue eliminada correctamente":"La ayuda no puede ser eliminada, tiene ayudas detalle",
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }

    public function EliminarAyudaDet(Request $request){
        if(!$request->ajax()) return redirect("/");
        try {
            DB::beginTransaction();
            $Eliminada = false;
            $ayuda = AyudasKastenDet::find($request->params['id']);
          
            $urlImagen = str_replace("/storage/ayudas",'',$ayuda->Imagen);
            if($urlImagen !== null){
                Storage::disk('ayudas')->delete($urlImagen);
            }
            $ayuda->delete();
            $Eliminada = true;
            DB::commit();
            return[
                'status'=>$Eliminada ? 201 : 500,
                'msg'=> $Eliminada ? "La ayuda fue eliminada correctamente": "La ayuda no se pudo eliminar".$ayuda,
                'ayuda'=>$ayuda,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return[
                'status'=>500,
                'msg'=> "Ocurrio un error en la transacción" .$e->getMessage(),
                'ayuda'=>[]
            ];
        }
    }
}

