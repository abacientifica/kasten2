<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\Roles;
use App\Model\RolesPermisos;

class RolesController extends Controller
{
    public function index(Request $request){
        $Roles = Roles::where('nombre','<>','');
        if($request->cNombre !=''){
            $Roles = $Roles->where('nombre','like','%'.$request->cNombre.'%');
        }
        if($request->cUrl !=''){
            $Roles = $Roles->where('slug','like','%'.$request->cUrl.'%');
        }
        if($request->nIdRol !='' ){
            $Roles = $Roles->where('id',$request->nIdRol);
        }
        $Roles = $Roles->get();
        return ["roles" => $Roles];
    }

    public function crearRol(Request $request)
    {

        $Rol = new Roles();
        $Rol->nombre = $request->cNombre;
        $Rol->slug = $request->cUrl;	
        $Rol->save();
        try {
            DB::beginTransaction();
            $listPermisos= $request->listPermisosFilter;
            if(sizeof($listPermisos)>0){
                DB::select($strSql);
                foreach($listPermisos as $key=>$value){
                    if($value['checked']){
                        $RolPermisoNew = new RolesPermisos();
                        $RolPermisoNew->id_permiso = $value['id'];
                        $RolPermisoNew->id_rol = $Rol->id;
                        $RolPermisoNew->save();
                    }   
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return['status'=>500,'msg'=>$e];
        }
        return[
            'rol'=>$Rol
        ];

    }

    public function actualizarRol(Request $request)
    {

        $Rol = Roles::findOrFail($request->IdRol);
        $Rol->nombre = $request->cNombre;
        $Rol->slug = $request->cUrl;	
        $Rol->save();
        try {
            DB::beginTransaction();
            $listPermisos= $request->listPermisosFilter;
            if(sizeof($listPermisos)>0){
                $strSql = "delete from roles_permisos where roles_permisos.id_rol = ".$Rol->id;
                DB::select($strSql);
                foreach($listPermisos as $key=>$value){
                    if($value['checked']){
                        $RolPermisoNew = new RolesPermisos();
                        $RolPermisoNew->id_permiso = $value['id'];
                        $RolPermisoNew->id_rol = $Rol->id;
                        $RolPermisoNew->save();
                    }   
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return['status'=>500,'msg'=>$e];
        }
        return[
            'rol'=>$Rol
        ];

    }

    public function ObtenerRol(Request $request){
        $Rol = Roles::findOrFail($request->id);
        return[
            'rol'=>$Rol
        ];
    }

    public function EditarRol(Request $request){
        $Rol = Roles::findOrFail($request->nIdPermiso);
        if($Rol){
            $Rol->nombre = $request->cNombre;
            $Rol->slug = $request->cUrl;	
            $Rol->save();
        }
        return [
            'rol'=>$Rol
        ];
    }

    public function getListarPermisosByRol(Request $request){
        $nIdRol = $request->nIdRol==''? 0:$request->nIdRol;
        $cNombre = $request->cNombre ==''? '':$request->cNombre;
        $Edit = ($request->bEdit) == false ? false: $request->bEdit;
        $Sql =" select    permiso.id,permiso.`nombre`,permiso.slug,if(rol_permiso.id_rol is not null , 1,0) as checked
                from permisos as permiso 
                LEFT OUTER JOIN roles_permisos  as rol_permiso on permiso.id = rol_permiso.id_permiso
                where  if(".$nIdRol." > 0 ,(rol_permiso.id_rol = ".$nIdRol." or  if(".$Edit."=1 ,rol_permiso.id_rol  is null,'' )),1) ";
        if($cNombre !=''){
            $Sql.= " and nombre like '%".$cNombre."%'";
        }
        $PermisosRol = DB::select($Sql);
        return[
            'permisosbyrol'=>$PermisosRol,
            'rol'=>$Sql
        ];
    }
}
