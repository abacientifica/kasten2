<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Permisos;
class PermisosController extends Controller
{
    public function index(Request $request){
        $Permisos = Permisos::where('nombre','<>','');
        if($request->cNombre !=''){
            $Permisos = $Permisos->where('nombre','like','%'.$request->cNombre.'%');
        }
        if($request->cUrl !=''){
            $Permisos = $Permisos->where('slug','like','%'.$request->cUrl.'%');
        }
        $Permisos = $Permisos->get();
        return ["permisos" => $Permisos];
    }

    public function crearPermiso(Request $request)
    {
        $Permiso = new Permisos();
        $Permiso->nombre = $request->cNombre;
        $Permiso->slug = $request->cUrl;	
        $Permiso->save();
        return[
            'permiso'=>$Permiso
        ];

    }

    public function ObtenerPermiso(Request $request){
        $Permiso = Permisos::findOrFail($request->id);
        return[
            'permiso'=>$Permiso
        ];
    }

    public function EditarPermiso(Request $request){
        $Permiso = Permisos::findOrFail($request->nIdPermiso);
        if($Permiso){
            $Permiso->nombre = $request->cNombre;
            $Permiso->slug = $request->cUrl;	
            $Permiso->save();
        }
        return [
            'permiso'=>$Permiso
        ];
    }
}
