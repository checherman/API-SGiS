<?php

namespace App\Http\Controllers\V1\Sistema;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use App\Models\Sistema\Rol;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response, \DB;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Response::json([ 'data' => []],200);
        //return Response::json(['error' => "NO EXSITE LA BASE"], 500);
        $parametros = Input::only('q','page','per_page');
        if ($parametros['q']) {
            $data =  Rol::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")->orWhere('nombre','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Rol::where("nombre","!=", "");
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
            'nombre'        => 'required|unique:roles',
        ];

        $inputs = Input::only('nombre', 'permisos');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {

            $rol = Usuario::create($inputs);

            return Response::json([ 'data' => $rol ],200);

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
        $object = Rol::find($id);

        if(!$object){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }
        $variable = DB::table("permiso_rol")->select("permiso_id")->where("rol_id", $id)->get();
        $permisos = [];
        foreach ($variable as $key => $value) {
            $permisos[] = $value->permiso_id;
        }
        $object->permisos = $permisos;
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
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required',
        ];

        $inputs = Input::only('nombre', 'permisos');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $object = Rol::find($id);
            $object->nombre =  $inputs['nombre'];

            $object->save();

            $object->permisos()->sync($inputs['permisos']);
            $object->permisos;

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
            $object = Rol::destroy($id);
            return Response::json(['data'=>$object],200);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }
}
