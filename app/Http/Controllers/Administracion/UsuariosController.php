<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\User;

class UsuariosController extends Controller {

    public function index( Request $request ) {
        try{
            if(!$request->ajax()) return redirect("/");
            $cUsuario = $request->cUsuario ;
            $cNombre = $request->cNombre;
            $cEmail = $request->cCorreo;
            $cEstado = $request->cEstado;
            $usuarios = User::where( 'Usuario', '<>', null);
            if($cUsuario != ''){
                $usuarios = $usuarios->where('Usuario','like','%'.$cUsuario.'%');
            }
            if($cNombre != ''){
                $usuarios = $usuarios->where('Nombres','like','%'.$cNombre.'%');
            }
            if($cEmail != ''){
                $usuarios = $usuarios->where('Email','like','%'.$cEmail.'%');
            }
            if($cEstado != ''){
                $usuarios = $usuarios->where('Inactivo',$cEstado);
            }
            $usuarios = $usuarios->get();
            return [
                'usuarios'=>$usuarios,
                'status'=>$cNombre
            ];
        }
        catch(Exception $e){

        }
    }

    public function getUsuario(Request $request){
        if(!$request->ajax()) return redirect("/");
        $usuario = User::findOrFail($request->id);
        $Rol = $usuario->rol == null ? 'Sin definir' : $usuario->rol->nombre;
        return[
            'usuario'=>$usuario,
            'rol'=> $Rol,
        ];
        
    }

    public function ActualizarUsuario(Request $request){
        if(!$request->ajax()) return redirect("/");
        $Datos = $request->fillCrearUsuario;
        $Usuario = User::findOrFail($Datos['cUsuario']);
        if($Usuario->Usuario !=''){
            $Usuario->Usuario = $Datos['cUsuario'];
            $Usuario->IdTercero = $Datos['nIdtercero'];
            if($Datos['cContrasena'] !=''){
                $Usuario->Contrasena = $Datos['cContrasena'];
                $Usuario->password = Hash::make($Datos['cContrasena']);
            }
            $Usuario->Nombres = $Datos['cNombres'];
            $Usuario->Apellidos = $Datos['cApellidos'];
            $Usuario->Cargo = $Datos['cCargo'];
            $Usuario->Email = $Datos['cCorreo'];
            $Usuario->IdRol = $Datos['nIdRol'];

            if(isset($Datos['cImagen']) && $Datos['cImagen']!=''){
                $imageInfo = explode(";base64,", $Datos['cImagen']); 
                $imgExt = str_replace('data:image/', '', $imageInfo[0]); 
                $image = str_replace(' ', '+', $imageInfo[1]); 
                $imageName = "avatar-".date("ymdhis").".".$imgExt; 
                Storage::disk('users')->put($imageName, base64_decode($image));
                $Usuario->imagen = '/storage/users/'.$imageName;
            }
            $Usuario->save();
            return[
                'usuario'=>$Usuario
            ];

        }
        else{
            return[
                'error'=>'No se puedo actualizar'
            ];
        }
    }

    public function ActualizarContrasenas(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            $Usuarios = User::where('Inactivo',0)->get();
            foreach($Usuarios as $user){
                $user->password = Hash::make($user->Contrasena);
                $user->save();
            }
            return[
                'msg'=>"Las contraseñas fueron actualizadas",
                'status'=>201
            ];
        }
        catch(Exception $e){
            return[
                'msg'=>"Ocurrio un error al actualizar las contraseñas",
                'status'=>500
            ];
        }
    }

    public function ActivarUsuario(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            $Usuario = User::findOrFail($request->id);
            if($Usuario){
                $Usuario->Inactivo =0;
                $Usuario->save();
            }
            return[
                'status'=>201
            ];
        }
        catch(Exception $e){
            return[
                'error'=>$e->getMessage()
            ];
        }

    }
    public function InaActivarUsuario(Request $request){
        if(!$request->ajax()) return redirect("/");
        try{
            $Usuario = User::findOrFail($request->id);
            if($Usuario){
                $Usuario->Inactivo =1;
                $Usuario->save();
            }
            return[
                'status'=>201
            ];
        }
        catch(Exception $e){
            return[
                'error'=>$e->getMessage()
            ];
        }
    }

}
