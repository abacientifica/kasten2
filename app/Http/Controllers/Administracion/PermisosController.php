<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Permisos;
use App\Model\PermisosUsuario;
use Illuminate\Support\Facades\DB;
use App\Events\CambioRol;
use App\User;

class PermisosController extends Controller
{
    public function index(Request $request){
        if(!$request->ajax()) return redirect("/");
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
        if(!$request->ajax()) return redirect("/");
        $Permiso = new Permisos();
        $Permiso->nombre = $request->cNombre;
        $Permiso->slug = $request->cUrl;	
        $Permiso->save();
        return[
            'permiso'=>$Permiso
        ];

    }

    public function ObtenerPermiso(Request $request){
        if(!$request->ajax()) return redirect("/");
        $Permiso = Permisos::findOrFail($request->id);
        return[
            'permiso'=>$Permiso
        ];
    }

    public function EditarPermiso(Request $request){
        if(!$request->ajax()) return redirect("/");
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

    public function ObtenerPermisosByUsuario(Request $request)
    {
        if(!$request->ajax()) return redirect("/");
        $nIdRol = $request->IdRol ? $request->IdRol : 0;
        $cUsuario = $request->Usuario;
        $Sql = "select permiso.id,permiso.`nombre`,permiso.slug,if(usuarios.Usuario is not null , 1,0) as checked from permisos as permiso
        LEFT OUTER JOIN permisos_usuario on permisos_usuario.id_permiso = permiso.id and permisos_usuario.usuario = '".$cUsuario."'
        LEFT OUTER JOIN usuarios on usuarios.Usuario = permisos_usuario.usuario 
        where permiso.id not in (select roles_permisos.id_permiso from roles_permisos where roles_permisos.id_rol = ".$nIdRol.")";
        $permisos = DB::select($Sql);
        return[
            'permisos'=>$permisos
        ];
    }

    public function setActualizarPermisosByUsuario(Request $request){
        if(!$request->ajax()) return redirect("/");
        $Permisos = $request->listPermisosFilter;
        $Usuario = $request->cUsuario;
        $User = User::find($Usuario);
        if(sizeof($Permisos)>0){
            try {
                DB::beginTransaction();
                DB::select("delete from permisos_usuario where usuario='".$Usuario."'");
                foreach($Permisos as $key=>$value){
                    if($value['checked']){
                        $Permiso = new PermisosUsuario();
                        $Permiso->id_permiso = $value['id'];
                        $Permiso->usuario = $Usuario;
                        $Permiso->save();
                    }   
                }
                DB::commit();
                broadcast(new CambioRol($User));
                return ['status'=>201,'msg'=>"Permisos Actualizados"];
            } catch (Exception $e) {
                DB::rollBack();
                return['status'=>500,'msg'=>$e];
            }
        }
    }

    public function ObtenerPermisosUsuario(Request $request){
        if(!$request->ajax()) return redirect("/");
        
        $IdRol = isset($request->nIdRol) ? $request->nIdRol : 0;
        if($IdRol == 0){
            $user = DB::select("select * from usuarios where Usuario ='".$request->cUsuario."'");
            $IdRol = $user[0]->IdRol >0 ? $user[0]->IdRol : 0;
        }
        $Sql = "select permisos.id,permisos.nombre,permisos.slug from usuarios
                LEFT JOIN permisos_usuario on permisos_usuario.usuario = usuarios.Usuario
                LEFT JOIN permisos on permisos.id = permisos_usuario.id_permiso
                where usuarios.Usuario = '".$request->cUsuario."'
                union 
                select permisos.id,permisos.nombre,permisos.slug from roles_permisos
                LEFT JOIN permisos on permisos.id = roles_permisos.id_permiso
                where  roles_permisos.id_rol = ".$IdRol;
        $Permisos = DB::select($Sql);
        return ["permisos"=>$Permisos];
    }
}
