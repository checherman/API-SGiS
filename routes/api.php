<?php

use Illuminate\Http\Request;
use App\Models\Sistema\Usuario;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServi ceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::post('obtener-token',    'V1\Sistema\AutenticacionController@autenticar');
Route::post('refresh-token',    'V1\Sistema\AutenticacionController@refreshToken');
Route::get('check-token',       'V1\Sistema\AutenticacionController@verificar');


Route::middleware('jwt')->group(function () {
    //Sistema
    Route::prefix('V1')->group(function () {
        Route::resource('usuarios',     'V1\Sistema\UsuarioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('roles',        'V1\Sistema\RolController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('permisos',     'V1\Sistema\PermisoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

        Route::resource('clues',                'V1\Catalogos\CluesController', ['only' => ['index']]);
        Route::resource('niveles-cones',        'V1\Catalogos\NivelesConesController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('estados-incidencias',  'V1\Catalogos\EstadosIncidenciasController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    });

    // Sync
    Route::prefix('sync')->namespace('Sync')->group(function () {
        Route::get('manual',     'SincronizacionController@manual');
        Route::get('auto',       'SincronizacionController@auto');
        Route::post('importar',  'SincronizacionController@importarSync');
        Route::post('confirmar', 'SincronizacionController@confirmarSync');
    });

});
