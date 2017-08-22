<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Controllers\ApiController;
use App\Models\Transacciones\Personas;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;

use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

/**
 * Controlador CensoPersona
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `CensoPersona`: Controlador  para el manejo de censo de personas
 *
 */
class CensoPersonaController extends ApiController
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
            $data =  Personas::with('derechohabientes','estados_embarazos','localidades')->where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['q']."%")
                    ->orWhere('paterno','LIKE',"%".$parametros['q']."%")
                    ->orWhere('materno','LIKE',"%".$parametros['q']."%")
                    ->orWhere('telefono','LIKE',"%".$parametros['q']."%")
                    ->orWhere('domicilio','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Personas::with('derechohabientes','estados_embarazos','localidades');
        }

        if(isset($parametros['page'])){

            $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
            $data = $data->paginate($resultadosPorPagina);
        } else {
            $data = $data->get();
        }

        return $this->respuestaVerTodo($data);
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
            'id'            => 'required|unique:personas',
            'nombre'        => 'required',
            'paterno'       => 'required',
            'materno'       => 'required',
            'telefono'      => 'required',
            'domicilio'     => 'required',

        ];

        $inputs = Input::only('id','nombre','paterno','materno','fecha_nacimiento','telefono','domicilio','estados_embarazos_id','derechohabientes_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = Personas::create($inputs);

            return Response::json([ 'data' => $data ],200);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
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
        $data = Personas::find($id);

        if(!$data){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->respuestaVerUno($data);
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
            'id'        => 'required',
            'nombre'    => 'required',
            'paterno'   => 'required',
            'materno'   => 'required',
            'telefono'  => 'required',
            'domicilio' => 'required',
        ];

        $inputs = Input::only('id','nombre','paterno','materno','fecha_nacimiento','telefono','domicilio','estados_embarazos_id','derechohabientes_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = Personas::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->paterno =  $inputs['paterno'];
            $data->materno =  $inputs['materno'];
            $data->fecha_nacimiento =  $inputs['fecha_nacimiento'];
            $data->telefono =  $inputs['telefono'];
            $data->domicilio =  $inputs['domicilio'];
            $data->estados_embarazos_id =  $inputs['estados_embarazos_id'];
            $data->derechohabientes_id =  $inputs['derechohabientes_id'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
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
            $data = Personas::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
