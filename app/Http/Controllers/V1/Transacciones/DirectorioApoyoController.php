<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Controllers\Controller;

use App\Models\Catalogos\DirectorioApoyos;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use App\Models\Sistema\Usuario;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response, \DB;

class DirectorioApoyoController extends Controller
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
            $data =  DirectorioApoyos::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('institucion','LIKE',"%".$parametros['q']."%")
                    ->orWhere('direccion','LIKE',"%".$parametros['q']."%")
                    ->orWhere('responsable','LIKE',"%".$parametros['q']."%")
                    ->orWhere('telefono','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  DirectorioApoyos::getModel();
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
            'email'         => "email",
            'unique'        => "unique"
        ];

        $reglas = [
            'institucion'       => 'required',
            'direccion'         => 'required',
            'responsable'       => 'required',
            'telefono'          => 'required',
            'correo'            => 'required|email'
        ];

        $inputs = Input::only('id','servidor_id','institucion', 'direccion', 'responsable', 'telefono', 'correo', 'municipios_id','apoyos');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $inputs['servidor_id'] = env("SERVIDOR_ID");
            $data = DirectorioApoyos::create($inputs);

            $data->apoyos()->sync($inputs['apoyos']);

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
        $object = DirectorioApoyos::find($id);

        if(!$object ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return Response::json([ 'data' => $object ], HttpResponse::HTTP_OK);
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
            'email'         => "email",
            'unique'        => "unique"
        ];

        $reglas = [
            'institucion'       => 'required',
            'direccion'         => 'required',
            'responsable'       => 'required',
            'telefono'          => 'required',
            'correo'            => 'required|email'
        ];

        $object = DirectorioApoyos::find($id);

        if(!$object){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        $inputs = Input::only('id','servidor_id','nombre', 'paterno', 'materno', 'celular', 'cargos_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $object->institucion =  $inputs['institucion'];
            $object->direccion =  $inputs['direccion'];
            $object->responsable =  $inputs['responsable'];
            $object->telefono =  $inputs['telefono'];
            $object->correo =  $inputs['correo'];
            $object->municipios_id =  $inputs['municipios_id'];
            $object->id =  $inputs['id'];

            $object->save();

            return Response::json([ 'data' => $object ],200);

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
			$object = DirectorioApoyos::destroy($id);
			return Response::json(['data'=>$object],200);
		} catch (Exception $e) {
		   return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
		}
    }
}
