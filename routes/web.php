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
    //Route::post('/usuarios/registrarImagenPerfil','FilesController@GuardarImagenPerfil');
    Route::put('/usuarios/inactivar/{id?}','Administracion\UsuariosController@InaActivarUsuario');
    Route::put('/usuarios/activar/{id?}','Administracion\UsuariosController@ActivarUsuario');
    Route::put('/usuarios/actualizarpass','Administracion\UsuariosController@ActualizarContrasenas');
    Route::get('/usuarios/cerrarsesion/','Administracion\UsuariosController@CerrarSesionUsuario');

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
    Route::post('/movimiento/setGenerarDocumento','Movimientos\MovimientosController@setGenerarDocumento');
    Route::put('/movimiento/anularMovimiento','Movimientos\MovimientosController@anularMovimiento');

    //Lista rutas plantillas clientes
    Route::get('/plantillas/clientes/lista','Plantillas\PlantillasClientesController@ListaPlantillas');
    Route::get('/plantillas/clientes/ObtenerPlantilla/{Id}/{filtros?}','Plantillas\PlantillasClientesController@ObtenerPlantilla');
    Route::post('/plantillas/clientes/nueva','Plantillas\PlantillasClientesController@CrearPlantilla');
    Route::get('/plantillas/ObtenerDatosHomolgar','Plantillas\PlantillasClientesController@CargarDatosHomologacion');
    Route::put('/plantillas/clientes/AsignarLista','Plantillas\PlantillasClientesController@AsignarLista');
    Route::put('/plantillas/clientes/GuardarCambiosGrilla','Plantillas\PlantillasClientesController@GuardarDatosEdit');
    Route::put('/plantillas/clientes/Editar','Plantillas\PlantillasClientesController@ActualizarPlantilla');
    Route::put('/plantillas/clientes/Autorizar','Plantillas\PlantillasClientesController@Autorizar');
    Route::put('/plantillas/clientes/DesAutorizar','Plantillas\PlantillasClientesController@DesAutorizar');
    Route::put('/plantillas/clientes/Anular','Plantillas\PlantillasClientesController@Anular');
    Route::put('/plantillas/clientes/EliminarDetalles','Plantillas\PlantillasClientesController@EliminarDetalles');
    Route::put('/plantillas/clientes/AutorizarDetalles','Plantillas\PlantillasClientesController@AutorizarDetalles');
    Route::put('/plantillas/clientes/DesAutorizarDetalles','Plantillas\PlantillasClientesController@DesAutorizarDetalles');
    Route::post('/plantillas/clientes/MarcarItemsVendidos','Plantillas\PlantillasClientesController@MarcarItemsVendidos');
    Route::post('/plantillas/clientes/ImportarArchivo/{Id}','Plantillas\PlantillasClientesController@ImportarArchivo');
    Route::put('/plantillas/clientes/AplicarCalculoFactor','Plantillas\PlantillasClientesController@AplicarCalculoFactor');
    Route::put('/plantillas/clientes/ProcesarHomologacion','Plantillas\PlantillasClientesController@ProcesarHomologacion');
    Route::put('/plantillas/clientes/AsignarCostoActual','Plantillas\PlantillasClientesController@AsignarCostoActual');
    Route::post('/plantillas/clientes/CrearCotizacion','Plantillas\PlantillasClientesController@CrearCotizacion');
    Route::post('/plantillas/clientes/GuardarFiltro','Plantillas\PlantillasClientesController@GuardarFiltro');
    Route::get('/plantillas/clientes/ObtenerFiltro','Plantillas\PlantillasClientesController@ObtenerFiltro');
    Route::put('/plantillas/clientes/CorrerFactores','Plantillas\PlantillasClientesController@CorrerFactoresPlantilla');
    Route::put('/plantillas/clientes/CorrerCostos','Plantillas\PlantillasClientesController@CorrerCostos');


    //Lista chequeo plantillas
    Route::get('/plantillas/listachequeo/{IdPlantilla}','Administracion\CheckController@GetListaCheck');
    Route::put('/plantillas/listachequeo/chequear','Administracion\CheckController@AplicarCheckPlantillas');

    //Lista rutas documentos
    Route::get('/documentos/lista','Administracion\DocumentosController@index');

    //Configuraciones Documentos
    Route::post('/documentos/GuardarConfig', 'Administracion\DocumentosController@GuardarCampoConfiguracion')->name('config.save');
    Route::get('/documentos/campos', 'Administracion\DocumentosController@CargarCamposconfiguracion')->name('campos.lista');
    Route::put('/documentos/campos/delete', 'Administracion\DocumentosController@EliminarCampoconfiguracion')->name('campos.delete');    
    Route::get('/conceptos/lista/{IdDoc}', 'ControladorGeneral@CargarConceptosDocumentos')->name('conceptos.lista');
    Route::get('/formaspago/lista', 'ControladorGeneral@CargarFormasDePago')->name('formaspago.lista');
    Route::get('/documentos/ObtenerDocTp', 'Administracion\DocumentosController@ObtenerDocumentosTp')->name('documentos.obtenertp');
    Route::get('/documentos/tipos/lista', 'Administracion\DocumentosController@ObtenerTiposDocumentos')->name('documentos.tipos');
    Route::get('/documentos/tipo/{id}', 'Administracion\DocumentosController@ObtenerTipoDocumento')->name('documento.tipo');
    Route::get('/documento/campos/', 'ControladorGeneral@CargarCamposTablas')->name('campos.documentos');

    //Configuracion campos documentos
    Route::get('/documentos/config/list/{IdDocumento}', 'Administracion\ConfiguracionDocumentos\ConfiguracionColumnasController@ObtenerConfiguracion')->name('configdoc.list');
    Route::put('/documentos/config/save', 'Administracion\ConfiguracionDocumentos\ConfiguracionColumnasController@GuardarConfiguracion')->name('configdoc.save'); 
    Route::get('/documentos/tablas/transacciones', 'Administracion\DocumentosController@ObtenerTablasTransaccionales')->name('tablas.transacciones');
    Route::put('/documentos/tablas/transacciones/save', 'Administracion\DocumentosController@GuardarTablasMaestrasDocumentos')->name('save.tablasmaestras');

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

    //Ayudas
    Route::get('/ayudas/lista', 'Administracion\AyudasKastenController@index');
    Route::post('/ayudas/guardar', 'Administracion\AyudasKastenController@registrarAyuda');
    Route::post('/ayudas/registrarAyudaKasten', 'Administracion\AyudasKastenController@registrarAyudaDet');
    Route::post('/ayudas/registrarAyudaItem', 'Administracion\AyudasKastenController@registrarAyudaItem');
    Route::get('/ayuda/ListaAyudasDet', 'Administracion\AyudasKastenController@ObtenerAyudas');
    Route::get('/ayuda/ListaItems', 'Administracion\AyudasKastenController@ObtenerAyudasItems');
    Route::get('/ayuda/ObtenerAyudaDet', 'Administracion\AyudasKastenController@ObtenerAyudaDet');
    Route::put('/ayuda/ActualizarAyuda', 'Administracion\AyudasKastenController@ActualizarAyuda');
    Route::put('/ayuda/ActualizarAyudaDet', 'Administracion\AyudasKastenController@ActualizarAyudaDet');
    Route::put('/ayuda/ActualizarAyudaDetItem', 'Administracion\AyudasKastenController@ActualizarAyudaDetItem');
    Route::put('/ayuda/EliminarAyuda', 'Administracion\AyudasKastenController@EliminarAyuda');
    Route::put('/ayuda/EliminarAyudaDet', 'Administracion\AyudasKastenController@EliminarAyudaDet');
    Route::get('/ayuda/ObtenerAyudasKasten', 'Administracion\AyudasKastenController@ObtenerAyudasKasten');

    //Novedades
    Route::get('/novedades/lista/{IdItem?}', 'Administracion\NovedadesController@getNovedadesItem');

    //Rutas Cotizaciones
    Route::group(['namespace' => 'Cotizaciones'], function() {
        Route::get('cotizacion/{IdCotizacion}','CotizacionesController@ObtenerCotizacion');
        Route::get('cotizaciones/filtros/usuario', 'CotizacionesController@FiltrosUsuarioLista');
        Route::post('cotizaciones/lista', 'CotizacionesController@ListaCotizaciones');
        Route::post('cotizaciones/index/filtros', 'CotizacionesController@GuardarFiltroIndex');
        Route::post('cotizaciones/lista', 'CotizacionesController@ListaCotizaciones');
        Route::get('cotizaciones/ObtenerCotizacion/{ IdCotizacion ?}', 'CotizacionesController@ObtenerCotizacion');
        Route::get('cotizaciones/getTiposCotizaciones', 'TiposController@getTiposCotizaciones');
        Route::post('cotizaciones/crear', 'CotizacionesController@CrearCotizacion');
        Route::put('cotizaciones/actualizar', 'CotizacionesController@Actualizar');
    });
    
});

Route::get('/{optional?}', function () {
    return view('app');
})->name('basepath')->where('optional','.*');