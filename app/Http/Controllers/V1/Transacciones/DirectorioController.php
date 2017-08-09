<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use App\Models\Sistema\Usuario;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response, \DB;

class DirectorioController extends Controller
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
             $usuarios =  Usuario::where('su',false)->whereNull('password')
                 ->where(function($query) use ($parametros) {
                 $query->where('id','LIKE',"%".$parametros['q']."%")->orWhere(DB::raw("CONCAT(nombre,' ',paterno,' ',materno)"),'LIKE',"%".$parametros['q']."%");
             });
        } else {
             $usuarios =  Usuario::where('su',false)->whereNull('password')->with('cargos')->with('clues');
        }

        if(isset($parametros['page'])){
            $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
            $usuarios = $usuarios->paginate($resultadosPorPagina);
        } else {
            $usuarios = $usuarios->get();
        }

        return Response::json([ 'data' => $usuarios],200);
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
            'id'            => 'required|email|unique:usuarios',
            'nombre'        => 'required',
            'paterno'       => 'required',
            'materno'       => 'required',
            'celular'       => 'required'
        ];

        $inputs = Input::only('id','servidor_id','nombre', 'paterno', 'materno', 'celular', 'cargos_id', 'clues');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $inputs['servidor_id'] = env("SERVIDOR_ID");
            $usuario = Usuario::create($inputs);

            return Response::json([ 'data' => $usuario ],200);

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
        $object = Usuario::find($id);

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
            'id'            => 'required|email|unique:usuarios,id,'.$id,
            'nombre'        => 'required',
            'paterno'       => 'required',
            'materno'       => 'required',
            'celular'       => 'required'
        ];
        $object = Usuario::find($id);

        if(!$object){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        $inputs = Input::only('id','servidor_id','nombre', 'paterno', 'materno', 'celular', 'cargos_id', 'clues');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $object->nombre =  $inputs['nombre'];
            $object->paterno =  $inputs['paterno'];
            $object->materno =  $inputs['materno'];
            $object->celular =  $inputs['celular'];
            $object->cargos_id =  $inputs['cargos_id'];
            $object->clues =  $inputs['clues'];
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
			$object = Usuario::destroy($id);
			return Response::json(['data'=>$object],200);
		} catch (Exception $e) {
		   return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
		}
    }
}
