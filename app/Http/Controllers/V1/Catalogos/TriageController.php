<?php

namespace App\Http\Controllers\V1\Catalogos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;
use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;

use App\Models\Catalogos\Triage;
use App\Models\Catalogos\TriageColores;
use App\Models\Catalogos\TriageSintomas;

/**
 * Controlador Triage
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-05-22
 *
 * Controlador `Triage`: Controlador  para el manejo de triage
 *
 */
class TriageController extends Controller
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
            $data =  Triage::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Triage::getModel()->with('triageSintomas');
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

        $success = false;
        $errors_main = array();
        DB::beginTransaction();

        try {

            $validacion = $this->ValidarParametros("", NULL, $datos);
            if($validacion != ""){
                return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
            }

            $data = new Triage;

            $data->nombre = $datos['nombre'];
            $data->descripcion = $datos['descripcion'];

            if ($data->save())
                $datos = (object) $datos;
            $this->AgregarDatos($datos, $data);
            $success = true;

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
     * Devuelve la información del registro especificado.
     *
     * @param  int  $id que corresponde al identificador del recurso a mostrar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function show($id){
        $data = Triage::with('triageSintomass')->find($id);

        if(!$data){
            return Response::json(array("status" => 204,"messages" => "No hay resultados"), 204);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data), 200);
        }
    }

    /**
     * Actualizar el  registro especificado en el la base de datos
     *
     * <h4>Request</h4>
     * Recibe un Input Request con el json de los datos
     *
     * @param  int  $id que corresponde al identificador del dato a actualizar
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 304, "messages": "No modificado"),status) </code>
     */
    public function update($id){
        $validacion = $this->ValidarParametros("", $id, Input::json()->all());
        if($validacion != ""){
            return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
        }
        $datos = Request::json();
        $success = false;
        DB::beginTransaction();
        try{
            $data = Triage::find($id);

            $data->nombre = $datos['nombre'];
            $data->descripcion = $datos['descripcion'];

            if ($data->save())
                $success = true;
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
     * Elimine el registro especificado del la base de datos (softdelete).
     *
     * @param  int  $id que corresponde al identificador del dato a eliminar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
     */
    public function destroy($id)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $data = Triage::find($id);
            if($data)
                $data->delete();
            $success = true;
        }
        catch (\Exception $e){
            return Response::json($e->getMessage(), 500);
        }
        if ($success){
            DB::commit();
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito","data" => $data), 200);
        }
        else {
            DB::rollback();
            return Response::json(array("status" => 404, "messages" => "No se encontro el registro"), 404);
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
            'unique' => 'unique'
        ];

        /*
        if($request['nivel_cone']) {
            $nivel_cone = $request['nivel_cone'];
        } else {
            $nivel_cone = NULL;
        }
        */
        $rules = [
            'nombre' => 'required|min:3|max:250|unique:triage',
            'descripcion' => 'required',
        ];

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
        //verificar si existe resguardos, en caso de que exista proceder a guardarlo
        if(property_exists($datos, "triage_sintomas")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->triage_sintomas, function($v){return $v !== null;});
            //borrar los datos previos de articulo para no duplicar información
            TriageSintomas::where("triage_id", $data->id)->delete();
            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                    DB::select("update triage_sintomas set deleted_at = null where triage_id = '$data->id' and nombre = '$value->nombre' ");
                    //si existe el elemento actualizar
                    $sintoma = TriageSintomas::where("triage_id", $data->id)->where("nombre", $value->nombre)->first();
                    //si no existe crear
                    if(!$sintoma)
                        $sintoma = new TriageSintomas;

                    $sintoma->triage_id 	= $data->id;
                    $sintoma->nombre        = $value->nombre;

                    if ($sintoma->save()){
                        if(property_exists($value, "resultado")){
                            dd($sintoma);
                            $sintoma->triageColores()->sync($datos);

                            //limpiar el arreglo de posibles nullos
//                            $detalle = array_filter($value->subcategorias_cie10, function($v){return $v !== null;});
                            //borrar los datos previos de articulo para no duplicar información
//
//                            SubCategoriasCie10::where("categorias_cie10_id", $categoria->id)->delete();
//
                            //recorrer cada elemento del arreglo
//                            foreach ($detalle as $key => $val) {
                                //validar que el valor no sea null
//                                if($val != null){
                                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
//                                    if(is_array($val))
//                                        $val = (object) $val;
//
                                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
//                                    DB::select("update subcategorias_cie10 set deleted_at = null where categorias_cie10_id = '$categoria->id' and nombre = '$val->nombre' ");
                                    //si existe el elemento actualizar
//                                    $subCategoria = SubCategoriasCie10::where("categorias_cie10_id", $categoria->id)->where("nombre", $val->nombre)->first();
                                    //si no existe crear
//                                    if(!$subCategoria)
//                                        $subCategoria = new SubCategoriasCie10;
//
//                                    $subCategoria->categorias_cie10_id 	= $categoria->id;
//                                    $subCategoria->nombre               = $val->nombre;
//
//                                    $subCategoria->save();
//                                }
//                            }
                        }
                    }

                }
            }


        }
    }
}
