<?php

namespace App\Http\Controllers\V1\Catalogos;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use App\Models\Catalogos\Rutas;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

/**
 * Controlador Ruta
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Ruta`: Controlador  para el manejo de catalogo de las rutas
 *
 */
class RutaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parametros = Input::only('q','page','per_page');
        if ($parametros['q']) {
            $data =  Rutas::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Rutas::where("id","!=", "");
        }


        if(isset($parametros['page'])){

            $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
            $data = $data->paginate($resultadosPorPagina);
        } else {
            $data = $data->get();
        }

        return Response::json([ 'data' => $data],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'                => 'required|unique:rutas',
            'clues_origen'          => 'required',
            'clues_destino'         => 'required',
            'tiempo_traslado'       => 'required',
            'distancia_traslado'    => 'required',
            'observaciones'         => 'required',
            'numeroLatitud_origen'         => 'required',
            'numeroLongitud_origen'         => 'required',
            'numeroLatitud_destino'         => 'required',
            'numeroLongitud_destino'         => 'required'
        ];

        $inputs = Input::only('nombre', 'clues_origen', 'clues_destino', 'tiempo_traslado', 'distancia_traslado', 'observaciones', 'numeroLatitud_origen', 'numeroLongitud_origen', 'numeroLatitud_destino', 'numeroLongitud_destino');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $data = Rutas::create($inputs);

            return Response::json([ 'data' => $data ],200);

        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Rutas::find($id);

        if(!$data){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return Response::json([ 'data' => $data ], HttpResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'                => 'required',
            'clues_origen'          => 'required',
            'clues_destino'         => 'required',
            'tiempo_traslado'       => 'required',
            'distancia_traslado'    => 'required',
            'observaciones'         => 'required',
            'numeroLatitud_origen'         => 'required',
            'numeroLongitud_origen'         => 'required',
            'numeroLatitud_destino'         => 'required',
            'numeroLongitud_destino'         => 'required'
        ];

        $inputs = Input::only('nombre', 'clues_origen', 'clues_destino', 'tiempo_traslado', 'distancia_traslado', 'observaciones', 'numeroLatitud_origen', 'numeroLongitud_origen', 'numeroLatitud_destino', 'numeroLongitud_destino');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $data = Rutas::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->clues_origen =  $inputs['clues_origen'];
            $data->clues_destino =  $inputs['clues_destino'];
            $data->tiempo_traslado =  $inputs['tiempo_traslado'];
            $data->distancia_traslado =  $inputs['distancia_traslado'];
            $data->observaciones =  $inputs['observaciones'];
            $data->numeroLatitud_origen =  $inputs['numeroLatitud_origen'];
            $data->numeroLongitud_origen =  $inputs['numeroLongitud_origen'];
            $data->numeroLatitud_destino =  $inputs['numeroLatitud_destino'];
            $data->numeroLongitud_destino =  $inputs['numeroLongitud_destino'];

            $data->save();
            return Response::json([ 'data' => $data ],200);

        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = Rutas::destroy($id);
            return Response::json(['data'=>$data],200);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }
}