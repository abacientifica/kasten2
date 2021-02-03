<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/authenticated/login','Auth\LoginController@login');
Route::post('/authenticated/logout','Auth\LoginController@logout');
Route::get('/authenticated/getRefrescarUsaurioAutentificado',function(){
    return \Auth::user();
});


//Lista rutas usuarios
Route::get('/usuarios/lista','Administracion\UsuariosController@index');
Route::get('/usuarios/getUsuario/{id?}','Administracion\UsuariosController@getUsuario');
Route::put('/usuarios/editar','Administracion\UsuariosController@ActualizarUsuario');
Route::post('/usuarios/registrarImagenPerfil','FilesController@GuardarImagenPerfil');
Route::put('/usuarios/inactivar/{id?}','Administracion\UsuariosController@InaActivarUsuario');
Route::put('/usuarios/activar/{id?}','Administracion\UsuariosController@ActivarUsuario');

//Lista rutas roles
Route::get('/roles/lista','Administracion\RolesController@index');
Route::post('/rol/crear','Administracion\RolesController@crearRol');
Route::get('/rol/get/{id}','Administracion\RolesController@ObtenerPermiso');
Route::post('/rol/editar','Administracion\RolesController@actualizarRol');
Route::get('/roles/getListarPermisosByRol','Administracion\RolesController@getListarPermisosByRol');
Route::get('/roles/getListarPermisosRol','Administracion\RolesController@getListarPermisosRol');
Route::get('/roles/ObtenerRolByUsuario/{id?}','Administracion\RolesController@ObtenerRolByUsuario');


//Lista rutas permisos
Route::get('/permisos/lista','Administracion\PermisosController@index');
Route::post('/permisos/crear','Administracion\PermisosController@crearPermiso');
Route::get('/permisos/get/{id}','Administracion\PermisosController@ObtenerPermiso');
Route::post('/permisos/editar','Administracion\PermisosController@EditarPermiso');
Route::get('/permiso/ObtenerPermisosByUsuario','Administracion\PermisosController@ObtenerPermisosByUsuario');
Route::post('/permiso/setActualizarPermisosByUsuario','Administracion\PermisosController@setActualizarPermisosByUsuario');
Route::get('/permiso/ObtenerPermisosUsuario','Administracion\PermisosController@ObtenerPermisosUsuario');

Route::get('/{optional?}', function () {
return view('app');
})->name('basepath')->where('optional','.*');