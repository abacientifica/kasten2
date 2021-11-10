<?php

namespace App\Http\Controllers\Utilidades;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventarioController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $datosInv = \Funciones::obtenerInventarioEstanterias();
        return [
            'datos'=>$datosInv,
            'status'=>200
        ];
    }

    public function obtenerConteos(Request $request){
        if(!$request->ajax()) return  redirect('/');
        $seccion = $request->seccion;
        $sector = $request->sector;
        $conteo2 = $request->conteo2 ? true:false;
        $conteo3 = $request->conteo3 ? true:false;
        $conteos = \Funciones::obtenerConteosInventario($seccion,$sector,$conteo2,$conteo3);
        return [
            'conteos'=>$conteos,
            'status'=>200
        ];
    }
}
