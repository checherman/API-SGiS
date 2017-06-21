<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use App\Models\Catalogos\Cargo;

/**
 * Controlador Cargo
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Cargos`: Controlador  para el manejo de catalogo cargos
 *
 */
class CargoController extends ApiController
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
            $data =  Cargo::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['q']."%")
                    ->orWhere('descripcion','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Cargo::where("id","!=", "");
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
            'nombre'        => 'required|unique:turnos',
            'descripcion'   => 'required',
        ];

        $inputs = Input::only('nombre', 'descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = Cargo::create($inputs);
            return $this->respuestaVerUno($data,201);

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
        $data = Cargo::find($id);

        if(!$data){
            return $this->respuestaError('No se encuentra el recurso que esta buscando.', 404);
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
            'nombre'        => 'required',
            'descripcion'   => 'required',
        ];

        $inputs = Input::only('nombre','descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = Cargo::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->descripcion =  $inputs['descripcion'];

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
            $data = Cargo::destroy($id);
            return $this->respuestaVerUno($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
