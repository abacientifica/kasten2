<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Terceros;
use Illuminate\Support\Facades\DB;

class TercerosController extends Controller
{
    public function index(Request $request)
    {
        if(!$request->ajax()){
            return  redirect('/');
        } 
        $filtro = $request->filtro;
        $terceros = Terceros::with('direcciones','asesorservcliente','asesor')->select('IdTercero','NombreCorto','IdTercero','IdAsesor','IdFormaPago','IdAsesorServicliente')
                                ->where('NombreCorto','like',"%".$filtro."%")
                                ->where('Inactivo',0)
                                ->where('Cliente',1)
                                ->orWhere('IdTercero','like',"%".$filtro."%")
                                ->orderBy('NombreCorto','asc')->take(10)->get();
        return [
            'terceros'=>$terceros
        ];
    }
}
