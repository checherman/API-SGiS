<?php

use Illuminate\Http\Request;
use App\Models\Sistema\Usuario;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::post('obtener-token',    'V1\Sistema\AutenticacionController@autenticar');
Route::post('refresh-token',    'V1\Sistema\AutenticacionController@refreshToken');
Route::get('check-token',       'V1\Sistema\AutenticacionController@verificar');


Route::group(['middleware' => 'jwt'], function () {
    //Sistema
    Route::group(['prefix' => 'V1'], function () {
        Route::resource('usuarios',     'V1\Sistema\UsuarioController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('roles',        'V1\Sistema\RolController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
        Route::resource('permisos',     'V1\Sistema\PermisoController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    });

    // sync
    Route::group(['prefix' => 'sync','namespace' => 'Sync'], function () {
        Route::get('manual',     'SincronizacionController@manual');
        Route::get('auto',       'SincronizacionController@auto');
        Route::post('importar',  'SincronizacionController@importarSync');
        Route::post('confirmar', 'SincronizacionController@confirmarSync');
    });

});
