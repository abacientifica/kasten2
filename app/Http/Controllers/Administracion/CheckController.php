<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\CheckPlantillas;
use App\Model\ListaCheckDocumentos;
use App\Events\CheckRegister;
class CheckController extends Controller
{
    public function GetListaCheck(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            $Lista = \Funciones::ObtenerListaChequeo($request->IdPlantilla);
            return [
                'status'=> 200,
                'msg'=>'Se genero la consulta con exito.',
                'lista_chequeo'=>$Lista
            ];
        }
        catch(Exception $e){
            return [
                'status'=> 501,
                'msg'=>'Ocurrio un error .'.$e->getMessage(),
                'lista_chequeo'=>null
            ];
        }
    }

    public function AplicarCheckPlantillas(Request $request){
        if(!$request->ajax()) return  redirect('/');
        try{
            DB::beginTransaction();
            $IdPlantilla = $request->IdPlantilla;
            $IdCheck = $request->IdCheck;
            $Comentarios = $request->Comentarios;
            $NewCheck = new  CheckPlantillas();
            $NewCheck->Id_Check = $IdCheck;
            $NewCheck->IdPlantilla = $IdPlantilla;
            $NewCheck->Usuario = \Auth::user()->Usuario;
            $NewCheck->FhCheck = date("Y-m-d H:i:s");
            $NewCheck->Anulado = 0 ;
            $NewCheck->Comentarios = $Comentarios;
            $NewCheck->save();
            $ListaCheck = \Funciones::ObtenerListaChequeo($IdPlantilla);
            event(new CheckRegister($NewCheck,$ListaCheck));
            DB::commit();
            return [
                'status'=> 200,
                'msg'=>'Se genero la consulta con exito.',
                'lista_chequeo'=>$ListaCheck
            ];
        }
        catch(Exception $e){
            DB::rollBack();
            return [
                'status'=> 501,
                'msg'=>'Ocurrio un error .'.$e->getMessage(),
                'lista_chequeo'=>null
            ];
        }
    }   
}
