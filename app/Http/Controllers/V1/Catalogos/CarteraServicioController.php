<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Catalogos\CarteraServicioNivelCone;
use Illuminate\Http\Response as HttpResponse;

use Request;
use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;

use App\Models\Catalogos\CarteraServicios;
use App\Models\Catalogos\Items;

/**
 * Controlador CarteraServicio
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `CarteraServicio`: Controlador  para el manejo de checklists
 *
 */
class CarteraServicioController extends Controller
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
            $data =  CarteraServicios::where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  CarteraServicios::getModel()->with('nivelesCones');
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

            $data = new CarteraServicios;
            $data->nombre = $datos['nombre'];

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
     * Devuelve la información del registro especificado.
     *
     * @param  int  $id que corresponde al identificador del recurso a mostrar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function show($id){
        $object = CarteraServicios::with('Items')->find($id);

        if(!$object ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        $nivelesCones = CarteraServicioNivelCone::where("cartera_servicios_id", $id)
            ->join('niveles_cones', 'niveles_cones.id', '=', 'cartera_servicio_nivel_cone.niveles_cones_id')
            ->get();

        $object->niveles_cones = $nivelesCones;

        return Response::json([ 'data' => $object ], HttpResponse::HTTP_OK);
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
            $data = CarteraServicios::find($id);
            $data->nombre = $datos->nombre;

            if ($data->save()){
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
            $data = CarteraServicios::find($id);
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

        $rules = [
            'nombre' => 'required|min:3|max:250|unique:cartera_servicios,nombre,'.$id.',id,deleted_at,NULL',
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
        if(property_exists($datos, "niveles_cones")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->niveles_cones, function($v){return $v !== null;});

            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //borrar los datos previos de articulo para no duplicar información
                    CarteraServicioNivelCone::where("cartera_servicios_id", $data->id)->delete();

                    //si existe actualizar
                    $nivelCone = CarteraServicioNivelCone::where("cartera_servicios_id", $data->id)->where("niveles_cones_id", $value->id)->first();

                    //si no existe crear
                    if(!$nivelCone)
                        $nivelCone = new CarteraServicioNivelCone;

                    $nivelCone->cartera_servicios_id 	= $data->id;
                    $nivelCone->niveles_cones_id        = $value->id;

                    if($nivelCone->save()){
                        $success = true;
                    }
                }
            }
        }


        //verificar si existe items, en caso de que exista proceder a guardarlo
        if(property_exists($datos, "items")){
            //limpiar el arreglo de posibles nullos
            $detalle = array_filter($datos->items, function($v){return $v !== null;});
            //borrar los datos previos de articulo para no duplicar información
            Items::where("cartera_servicios_id", $data->id)->delete();
            //recorrer cada elemento del arreglo
            foreach ($detalle as $key => $value) {
                //validar que el valor no sea null
                if($value != null){
                    //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                    if(is_array($value))
                        $value = (object) $value;

                    //comprobar que el dato que se envio no exista o este borrado, si existe y esta borrado poner en activo nuevamente
                    DB::update("update items set deleted_at = null where cartera_servicios_id = $data->id and nombre = '$value->nombre' ");
                    //si existe el elemento actualizar
                    $items = Items::where("cartera_servicios_id", $data->id)->where("nombre", $value->nombre)->first();
                    //si no existe crear
                    if(!$items)
                        $items = new Items;

                    $items->cartera_servicios_id 	= $data->id;
                    $items->tipos_items_id 	        = $value->tipos_items_id;
                    $items->nombre                  = $value->nombre;

                    if($items->save()){
                        $success = true;
                    }
                }
            }
        }

        return $success;
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
    public function showEstadoFuerza(){

        $nivelCone = Input::only('nivel-cone');

        $data = CarteraServicioNivelCone::where('niveles_cones_id', '=', $nivelCone)->with("carteraServicio")->get();

        if(!$data ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return Response::json([ 'data' => $data ], HttpResponse::HTTP_OK);
    }
}
