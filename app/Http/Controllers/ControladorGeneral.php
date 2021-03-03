<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Conceptos;

class ControladorGeneral extends Controller
{
    
    public function ObtenerAsesores(Request $request)
    {
        if(!$request->ajax())  return  redirect('/'); 
        $Asesores = DB::select("select * from asesores where Inactivo =0");
        return [
            'asesores'=>$Asesores
        ];
    }

    public function CargarConceptosDocumentos(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $conceptos =  Conceptos::select('conceptos.IdConcepto','NmConcepto')
                        ->leftjoin('conceptosdocumentos','conceptosdocumentos.IdConcepto','=','conceptos.IdConcepto')
                        ->where("conceptosdocumentos.IdDocumento",'=',$request->IdDoc)
                        ->where('conceptos.Inactivo',0)->get();
        return [
            'conceptos'=>$conceptos
        ];
    }

    public function CargarFormasDePago(Request $request){
        if(!$request->ajax())  return  redirect('/'); 
        $FormasPago =  DB::select("select * from formaspago");
        return [
            'formas_pago'=>$FormasPago
        ];
    }
}
