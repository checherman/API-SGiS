<?php

namespace App\Http\Controllers\V1\Sistema;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use App\Models\Sistema\Permiso;

use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;



class PermisoController extends Controller
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
            $data =  Permiso::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('descripcion','LIKE',"%".$parametros['q']."%")
                    ->orWhere('grupo','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Permiso::where("id","!=", "");
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
            'id'        => 'required',
            'descripcion'        => 'required',
        ];

        $inputs = Input::only('id', 'descripcion', 'grupo', 'su');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {

            $data = Permiso::create($inputs);

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
        $data = Permiso::find($id);

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
            'id'        => 'required',
            'descripcion'        => 'required',
        ];

        $inputs = Input::only('id', 'descripcion', 'grupo', 'su');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $data = Permiso::find($id);
            $data->id =  $inputs['id'];
            $data->descripcion =  $inputs['descripcion'];
            $data->grupo =  $inputs['grupo'];
            $data->su =  $inputs['su'];

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
            $data = Permiso::destroy($id);
            return Response::json(['data'=>$data],200);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }
}
