<?php

use Illuminate\Http\Request;
use App\Models\Sistema\Usuario;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RoutesProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('obtener-token',    'V1\Sistema\AutenticacionController@autenticar');
Route::post('refresh-token',    'V1\Sistema\AutenticacionController@refreshToken');
Route::get('check-token',       'V1\Sistema\AutenticacionController@verificar');

//Autocomplete
Route::get('grupo-permiso',             'AutoCompleteController@grupo_permiso');
Route::get('clues-auto',                'AutoCompleteController@clues');
Route::get('jurisdiccion-clues',        'AutoCompleteController@jurisdiccion_clues');
Route::get('personas-auto',             'AutoCompleteController@personas');

Route::middleware('jwt')->group(function () {
    //Sistema
    Route::prefix('V1')->group(function () {
        Route::resource('usuarios',     'V1\Sistema\UsuarioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('roles',        'V1\Sistema\RolController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('permisos',     'V1\Sistema\PermisoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

        //Catalogos
        Route::resource('clues',                    'V1\Catalogos\CluesController', ['only' => ['index', 'show']]);
        Route::resource('jurisdicciones',           'V1\Catalogos\JurisdiccionController', ['only' => ['index']]);
        Route::resource('localidades',              'V1\Catalogos\LocalidadController', ['only' => ['index']]);
        Route::resource('municipios',               'V1\Catalogos\MunicipioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('estados-incidencias',      'V1\Catalogos\EstadoIncidenciaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('derechohabientes',         'V1\Catalogos\DerechohabienteController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('estados-embarazos',        'V1\Catalogos\EstadoEmbarazoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('estados-pacientes',        'V1\Catalogos\EstadoPacienteController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('valoraciones-pacientes',   'V1\Catalogos\ValoracionPacienteController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('parentescos',              'V1\Catalogos\ParentescoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('metodos-planificacion',    'V1\Catalogos\MetodoPlanificacionController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('tipos-items',              'V1\Catalogos\TipoItemController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('turnos',                   'V1\Catalogos\TurnoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('cargos',                   'V1\Catalogos\CargoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('apoyos',                   'V1\Catalogos\ApoyoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

        Route::resource('rutas',                'V1\Catalogos\RutaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('cartera-servicios',    'V1\Catalogos\CarteraServicioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('niveles-cones',        'V1\Catalogos\NivelConeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('grupos-cie10',         'V1\Catalogos\GrupoCie10Controller', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

        Route::resource('triage-colores',       'V1\Catalogos\TriageColorController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('triage',               'V1\Catalogos\TriageController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

        //Transacciones
        Route::resource('base-conocimientos',   'V1\Transacciones\BaseConocimientoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('directorio',           'V1\Transacciones\DirectorioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('directorio-apoyos',    'V1\Transacciones\DirectorioApoyoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('incidencias',          'V1\Transacciones\IncidenciaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('censo-personas',       'V1\Transacciones\CensoPersonaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

        //Funcionalidad
        Route::get('estado-fuerza/{nivelesCones}',                'V1\Catalogos\CarteraServicioController@showEstadoFuerza');

        //movil

    });

    // Sync
    Route::prefix('sync')->namespace('Sync')->group(function () {
        Route::get('manual',     'SincronizacionController@manual');
        Route::get('auto',       'SincronizacionController@auto');
        Route::post('importar',  'SincronizacionController@importarSync');
        Route::post('confirmar', 'SincronizacionController@confirmarSync');
    });

});
