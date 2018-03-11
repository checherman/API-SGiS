<?php

/**
 * Route
 *
 * @package    Plataforma API
 * @subpackage Routes* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
 * @created    2015-07-20
 *
 * Rutas de la aplicación
 *
 * Aquí es donde se registran todas las rutas para la aplicación.
 * Simplemente decirle a laravel los URI que debe responder y poner los filtros que se ejecutará cuando se solicita la URI .
 *
 */

Route::get("/v1/pdf", 			 "V1\ExportController@getPDF");
Route::post("/v1/html-pdf", 	 "V1\ExportController@setHTML");

//Autocomplete
Route::get('/v1/grupo-permiso',             'AutoCompleteController@grupo_permiso');
Route::get('/v1/clues-auto',                'AutoCompleteController@clues');
Route::get('/v1/clues-fuerza-auto',         'AutoCompleteController@clues_fuerza');
Route::get('/v1/jurisdiccion-clues',        'AutoCompleteController@jurisdiccion_clues');
Route::get('/v1/personas-auto',             'AutoCompleteController@personas');
Route::get('/v1/subcategoriascie10-auto',   'AutoCompleteController@subcategoriascie10');
Route::get('/v1/usuarios-auto',             'AutoCompleteController@usuarios');

//Inicia bloque de OAUTH
/**
 * si se tiene un token y expira podemos renovar con el refresh token proporcionado
 */
Route::post("refresh-token",     "V1\Sistema\SisOauthController@refreshToken");
Route::post("v1/refresh-token",  "V1\Sistema\SisOauthController@refreshToken");
/**
 * Obtener el token y refresh token con las credenciales de un usuario y el CLIENT_ID y SECRET_ID de la aplicacion cliente
 */

Route::post("/v1/signin",            "V1\Sistema\SisOauthController@accessToken");
Route::post('/form-contacto',        'V1\Sistema\SisOauthController@contacto');


Route::middleware('token')->prefix("v1")->group(function(){

    Route::post("validacion-cuenta",     	"V1\Sistema\SisOauthController@validarCuenta");
    Route::get("perfil/{id}",				"V1\Sistema\SisOauthController@perfil");
    Route::put("perfil/{id}",				"V1\Sistema\SisOauthController@actualizarPerfil");
    Route::put("actualizar-foto/{email}",	"V1\Sistema\SisOauthController@actualizarFoto");
    Route::get("permiso",   				"V1\Sistema\SisModuloController@permiso");
    Route::post("permisos-autorizados", 	"V1\Sistema\SisOauthController@permisosAutorizados");

//    Route::get("descargar-app",   			"v1\Catalogos\CatalogosController@descargarApp");
//    Route::get("puesto-usuario/{id}",   	"v1\Catalogos\CatalogosController@puestoUsuario");
//    Route::get("lista-persona/{id}",   		"v1\Catalogos\CatalogosController@personas");

    Route::resource("tipo-medio",         	"V1\Catalogos\TipoMedioController");
    Route::resource("tipo-red-social", 		"V1\Catalogos\TipoRedSocialController");


    Route::resource("notificacion",         "V1\Sistema\NotificacionController");
});

//Route::get("/v1/comprimir/{id}", "v1\Catalogos\CatalogosController@comprimir");
// Fin OAUTH

// Inicia rutas del sistema
/**
 * rutas api v1 protegidas con middleware tokenPermiso que comprueba si el usuario tiene o no permisos para el recurso solicitado
 */
Route::middleware('tokenPermiso')->prefix("v1")->group(function(){

    //sistema
    Route::resource("sisModulo",  			        "V1\Sistema\SisModuloController");
    Route::resource("sisUsuario", 			        "V1\Sistema\SisUsuarioController");
    Route::resource("sisGrupo",   			        "V1\Sistema\SisGrupoController");
    Route::resource("sisReporte",   		        "V1\Sistema\SisReporteController");
    Route::resource("sisDashboard",   		        "V1\Sistema\SisDashboardController");
    Route::resource("version-app",   		        "V1\Sistema\VersionAppController");
    Route::get("descargar-app",                     "V1\Sistema\VersionAppController@descargarApp");

    //Route::get("descargar-app",                     "v1\Sistema\VersionAppController@descargarApp");

    //Reportes y Dashboard
    Route::get("dashboard",   			                    "V1\Dashboard\DashboardController@index");
    Route::get("reportes/incidencias-ingresos",             "V1\Reportes\ReporteController@incidenciasIngreso");
    Route::get("reportes/incidencias-referencias",          "V1\Reportes\ReporteController@incidenciasReferencia");
    Route::get("reportes/incidencias-altas",                "V1\Reportes\ReporteController@incidenciasAlta");
    Route::get("reportes/estado-fuerza",                    "V1\Reportes\ReporteController@estadoFuerza");
});

// Inicia rutas de catalogos
/**
 * rutas api v1 protegidas con middleware token
 */
Route::middleware('tokenPermiso')->prefix("v1")->group(function(){
    //catalogos
    Route::resource("tipo-medio",         	"V1\Catalogos\TipoMedioController",        ['only' => ['show', 'store','update','destroy']]);
    Route::resource("tipo-red-social", 		"V1\Catalogos\TipoRedSocialController",    ['only' => ['show', 'store','update','destroy']]);

    Route::resource("paises", 		   	        "V1\Catalogos\PaisController");
    Route::resource("estados",  	   	        "v1\Catalogos\EstadoController");
    Route::resource('clues',                    'V1\Catalogos\CluesController', ['only' => ['index', 'show']]);
    Route::resource('jurisdicciones',           'V1\Catalogos\JurisdiccionController', ['only' => ['index']]);
    Route::resource('localidades',              'V1\Catalogos\LocalidadController', ['only' => ['index']]);
    Route::resource('municipios',               'V1\Catalogos\MunicipioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    Route::resource('estados-incidencias',          'V1\Catalogos\EstadoIncidenciaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('estados-embarazos',            'V1\Catalogos\EstadoEmbarazoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('estados-pacientes',            'V1\Catalogos\EstadoPacienteController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('derechohabientes',             'V1\Catalogos\DerechohabienteController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('ubicaciones-pacientes',        'V1\Catalogos\UbicacionPacienteController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('parentescos',                  'V1\Catalogos\ParentescoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('metodos-planificacion',        'V1\Catalogos\MetodoPlanificacionController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('tipos-items',                  'V1\Catalogos\TipoItemController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource("tipos-notificaciones",         "V1\Catalogos\TipoNotificacionController",    ['only' => ['index', 'show', 'store','update','destroy']]);
    Route::resource('turnos',                       'V1\Catalogos\TurnoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('cargos',                       'V1\Catalogos\CargoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('apoyos',                       'V1\Catalogos\ApoyoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('tipos-altas',                  'V1\Catalogos\TipoAltaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    Route::resource('rutas',                'V1\Catalogos\RutaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('cartera-servicios',    'V1\Catalogos\CarteraServicioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('niveles-cones',        'V1\Catalogos\NivelConeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('grupos-cie10',         'V1\Catalogos\GrupoCie10Controller', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    Route::resource('triage-colores',       'V1\Catalogos\TriageColorController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('triage',               'V1\Catalogos\TriageController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    //transaccion
    Route::resource('base-conocimientos',   'V1\Transacciones\BaseConocimientoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('directorio',           'V1\Transacciones\DirectorioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('directorio-apoyos',    'V1\Transacciones\DirectorioApoyoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('incidencias',          'V1\Transacciones\IncidenciaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('visitas-puerperales',    'V1\Transacciones\VisitaPuerperalController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('censo-personas',       'V1\Transacciones\CensoPersonaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    Route::resource('estados-fuerza',       'V1\Transacciones\EstadoFuerzaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
});

Route::group(array('prefix'=>'v1/subir-archivo' , "middleware" => "token"),function(){
    Route::post('subir',    array('uses'=>'SubirArchivosController@subir'));
    Route::post('mostrar',  array('uses'=>'SubirArchivosController@mostrar'));
    Route::post('eliminar', array('uses'=>'SubirArchivosController@eliminar'));
});
//end rutas api v1

