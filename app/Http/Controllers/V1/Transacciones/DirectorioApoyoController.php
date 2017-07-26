<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Controllers\Controller;

use App\Models\Catalogos\ApoyoDirectorioApoyo;
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

        $datos = Input::json()->all();

        $errors_main = array();
        DB::beginTransaction();

        try {
            $validacion = $this->ValidarParametros("", NULL, $datos);
            if ($validacion != "") {
                return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
            }

            $data = new DirectorioApoyos;
            $data->institucion = $datos['institucion'];
            $data->responsable = $datos['responsable'];
            $data->direccion = $datos['direccion'];
            $data->correo = $datos['correo'];
            $data->telefono = $datos['telefono'];
            $data->municipios_id = $datos['municipios_id'];

            if ($data->save()) {
                $datos = (object)$datos;
                $success = $this->AgregarDatos($datos, $data);
            }

        } catch (\Exception $e){
            return Response::json($e->getMessage(), 500);
        }

        if ($success){
            DB::commit();
            return Response::json(array("status" => 201,"messages" => "Creado","data" => $data), 201);
        } else{
            DB::rollback();
            return Response::json(array("status" => 409,"messages" => "Conflicto"), 409);
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
        $data = DirectorioApoyos::find($id);

        if(!$data ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        $apoyos = ApoyoDirectorioApoyo::where("directorio_apoyos_id", $id)
            ->join('apoyos', 'apoyos.id', '=', 'apoyo_directorio_apoyo.apoyos_id')
            ->get();

        $data->apoyos = $apoyos;

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
        $datos = Request::json()->all();

        $validacion = $this->ValidarParametros("", $id, $datos);
        if($validacion != ""){
            return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
        }

        if(is_array($datos))
            $datos = (object) $datos;

        $success = false;

        DB::beginTransaction();
        try{
            $data = DirectorioApoyos::find($id);
            $data->institucion = $datos->institucion;
            $data->responsable = $datos->responsable;
            $data->direccion = $datos->direccion;
            $data->correo = $datos->correo;
            $data->telefono = $datos->telefono;
            $data->municipios_id = $datos->municipios_id;


            if ($data->save()) {
                $datos = (object)$datos;
                $success = $this->AgregarDatos($datos, $data);
            }
        }
        catch (\Exception $e){
            return Response::json($e->getMessage(), 500);
        }

        if ($success){
            DB::commit();
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
        }
        else {
            DB::rollback();
            return Response::json(array("status" => 304, "messages" => "No modificado"),304);
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

    /**
     * Validad los parametros recibidos, Esto no tiene ruta de acceso es un metodo privado del controlador.
     *
     * @param  Request  $request que corresponde a los parametros enviados por el cliente
     *
     * @return Response
     * <code> Respuesta Error json con los errores encontrados </code>
     */
    private function ValidarParametros($key, $id, $request){

        $messages = [
            'required' => 'required',
            'email'         => "email",
            'unique' => 'unique'
        ];

        $rules = [
            'responsable' => 'required',
            'direccion' => 'required',
            'correo'        => 'required|email',
        ];
        //dd($rules);
        $v = Validator::make($request, $rules, $messages);

        if ($v->fails()){
            $mensages_validacion = array();
            foreach ($v->errors()->messages() as $indice => $item) { // todos los mensajes de todos los campos
                $msg_validacion = array();
                foreach ($item as $msg) {
                    array_push($msg_validacion, $msg);
                }
                array_push($mensages_validacion, array($indice.''.$key => $msg_validacion));
            }
            return $mensages_validacion;
        }else{
            return ;
        }
    }

    private function AgregarDatos($datos, $data){

        $success = false;
        //verificar si existe items, en caso de que exista proceder a guardarlo
        if(property_exists($datos, "apoyos")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->apoyos, function($v){return $v !== null;});

            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //borrar los datos previos de articulo para no duplicar información
                    ApoyoDirectorioApoyo::where("apoyos_id", $data->id)->delete();

                    //si existe actualizar
                    $apoyos = ApoyoDirectorioApoyo::where("directorio_apoyos_id", $data->id)->where("apoyos_id", $value->id)->first();

                    //si no existe crear
                    if(!$apoyos)
                        $apoyos = new ApoyoDirectorioApoyo;

                    $apoyos->directorio_apoyos_id 	= $data->id;
                    $apoyos->apoyos_id              = $value->id;

                    if($apoyos->save()){
                        $success = true;
                    }
                }
            }
        }

        return $success;

    }
}
