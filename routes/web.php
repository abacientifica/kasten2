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
Route::group(['middleware' => ['auth']], function () {
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
    Route::put('/usuarios/actualizarpass','Administracion\UsuariosController@ActualizarContrasenas');

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

    //Lista rutas movimientos
    Route::get('/movimientos/lista','Movimientos\MovimientosController@index');
    Route::get('/movimiento/{IdDoc}/{IdMov}','Movimientos\MovimientosController@ObtenerMovimiento');
    Route::post('/movimiento/nuevo','Movimientos\MovimientosController@RegistrarMovimiento');
    Route::put('/movimiento/autorizar','Movimientos\MovimientosController@Autorizar');
    Route::put('/movimiento/editar','Movimientos\MovimientosController@ActualizarMovimiento');
    Route::put('/movimiento/notificar','Movimientos\MovimientosController@NotificarMovimiento');
    Route::put('/movimiento/EliminarDet','Movimientos\MovimientosController@EliminarMovimientoDet');
    Route::put('/movimiento/agregarProducto','Movimientos\MovimientosController@AgregarProducto');

    //Lista rutas documentos
    Route::get('/documentos/lista','Administracion\DocumentosController@index');

    //Configuraciones Documentos
    Route::post('/documentos/GuardarConfig', 'Administracion\DocumentosController@GuardarCampoConfiguracion')->name('config.save');
    Route::get('/documentos/campos', 'Administracion\DocumentosController@CargarCamposconfiguracion')->name('campos.lista');
    Route::put('/documentos/campos/delete', 'Administracion\DocumentosController@EliminarCampoconfiguracion')->name('campos.delete');    
    Route::get('/conceptos/lista/{IdDoc}', 'ControladorGeneral@CargarConceptosDocumentos')->name('conceptos.lista');
    Route::get('/formaspago/lista', 'ControladorGeneral@CargarFormasDePago')->name('formaspago.lista');
    Route::get('/documentos/ObtenerDocTp', 'Administracion\DocumentosController@ObtenerDocumentosTp')->name('documentos.obtenertp');

    //Rutas log de acciones
    Route::get('/log/lista', 'ControladorGeneral@LogMovimientos');


    //Listado de rutas terceros
    Route::get('/terceros/lista', 'Administracion\TercerosController@index')->name('terceros.lista');

    //Listado de rutas asesores
    Route::get('/asesores/lista', 'ControladorGeneral@ObtenerAsesores')->name('asesores.lista');

    //Listado de rutas Lista precios
    Route::get('/listaprecios/lista', 'Administracion\ListaPreciosController@index')->name('listaprecios.lista');

    //Rutas dashboard
    Route::get('/dashboard', 'ControladorGeneral@DashboardHome')->name('dashboard');

    //Rutas Reportes
    Route::get('/reporte/ventas', 'Administracion\ReportesController@ReporteVentas');

    //Reporte de ventas grilla
    Route::get('/ventasGrilla', 'AdministracionReportesController@index');

    //Ruta Direcciones
    Route::get('/direcciones/obtenerDireccion', 'ControladorGeneral@ObtenerDireccion');

    //Rutas del chat
    
    Route::get('/chat/getListarContactos', 'Administracion\ChatController@ListaContactos');
    Route::get('/chat/getListarConversaciones', 'Administracion\ChatController@ListaConversaciones');
    Route::post('/chat/setRegistrarMensaje', 'Administracion\ChatController@setRegistrarMensaje');
});
Route::get('/{optional?}', function () {
return view('app');
})->name('basepath')->where('optional','.*');