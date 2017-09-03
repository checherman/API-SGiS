<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Catalogos\CarteraServicios;
use App\Models\Catalogos\NivelesCones;
use App\Models\Transacciones\RespuestasEstadosFuerza;
use DateTime;
use Illuminate\Http\Response as HttpResponse;

use PhpParser\Node\Expr\Cast\Object_;
use Request;
use \Validator,\Hash, \Response, \DB;
use Illuminate\Support\Facades\Input;

/**
 * Controlador EstadoFuerza
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `EstadoFuerza`: Controlador  para el manejo de Estados de Fuerza en los hospitales
 *
 */
class EstadoFuerzaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parametros = Input::only('q','page','per_page', 'clues');

        if ($parametros['q']) {
            if ($parametros['clues']) {
                $data = RespuestasEstadosFuerza::with('cartera_servicios','clues','items', 'turnos')
                    ->where('respuestas_estados_fuerza.clues', $parametros['clues'])
                    ->where(function ($query) use ($parametros) {
                    $query->where('id', 'LIKE', "%" . $parametros['q'] . "%")
                        ->orWhere('clues', 'LIKE', "%" . $parametros['q'] . "%");
                });
            }else{
                $data = RespuestasEstadosFuerza::with('cartera_servicios','clues','items', 'turnos')
                    ->where(function ($query) use ($parametros) {
                        $query->where('id', 'LIKE', "%" . $parametros['q'] . "%")
                            ->orWhere('clues', 'LIKE', "%" . $parametros['q'] . "%");
                    });
            }
        } else {
            if ($parametros['clues']) {
                $data = RespuestasEstadosFuerza::where('respuestas_estados_fuerza.clues', $parametros['clues'])
                    ->with('cartera_servicios','clues','items', 'turnos');
            }else{
                $data = RespuestasEstadosFuerza::with('cartera_servicios','clues','items', 'turnos');
            }
        }
        $data->groupBy('created_at');

        if(isset($parametros['page'])){

            $resultadosPorPagina = isset($parametros["per_page"])? $parametros["per_page"] : 20;
            $data = $data->paginate($resultadosPorPagina);
        } else {
            $data = $data->get();
        }


        return Response::json([ 'data' => $data],200);
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

        $datos = (object) Input::json()->all();

        $errors_main = array();
        DB::beginTransaction();

        try {
//            $validacion = $this->ValidarParametros("", NULL, $datos);
//            if ($validacion != "") {
//                return Response::json(['error' => $validacion], HttpResponse::HTTP_CONFLICT);
//            }
            $data = new RespuestasEstadosFuerza;
            $success = $this->AgregarDatosRespuesta($datos, $data);

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

        $nivelesCones = NivelesCones::find($id);
        if(!$nivelesCones ){

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }
        $ahora = date("Y-m-d h:i:s");
        $data = collect();
        $data->put('clues', "");
        $data->put('usuarios_id', "");
        $data->put('turnos_id', "");
        $data->put('created_at', $ahora);
        $data->put('usuario', "");
        $carteraServicios = $nivelesCones->carteraServicio()->with("items")->get();
        $data->put('cartera_servicios', $carteraServicios);

        foreach ($data['cartera_servicios'] as $key => $value) {
            foreach ($value->items as $keyI => $item) {
                $item->respuesta = "";
            }
        }



        return Response::json([ 'data' => $data ], HttpResponse::HTTP_OK);
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

    private function AgregarDatosRespuesta($datos, $data){
        $success = false;
        $datos = (object)$datos;

        //verificar si existe cartera servicios, en caso de que exista proceder a guardarlo
//        if(property_exists($datos, "data")){
//            //limpiar el arreglo de posibles nullos
//            $detalleData = array_filter($datos->data, function($v){return $v !== null;});
//            dd($detalleData);
//            if(is_array($detalleData))
//                $detalleData = (object) $detalleData;

                //verificar si existe cartera servicios, en caso de que exista proceder a guardarlo
                if (property_exists($datos, "cartera_servicios")) {
                    //limpiar el arreglo de posibles nullos
                    $detalleCartera = array_filter($datos->cartera_servicios, function ($v) {return $v !== null;});

                    if (is_array($detalleCartera))
                        $detalleCartera = (object)$detalleCartera;

                    //recorrer cada elemento del arreglo
                    foreach ($detalleCartera as $key => $valueCartera) {
                        //validar que el valor no sea null
                        if ($valueCartera != null) {
                            //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                            if (is_array($valueCartera))
                                $valueCartera = (object)$valueCartera;

                            //verificar si existe items, en caso de que exista proceder a guardarlo
                            if (property_exists($valueCartera, "items")) {
                                //limpiar el arreglo de posibles nullos
                                $detalleItems = array_filter($valueCartera->items, function ($v) {return $v !== null;});

                                if (is_array($detalleItems))
                                    $detalleItems = (object)$detalleItems;

                                //recorrer cada elemento del arreglo
                                foreach ($detalleItems as $key => $valueItems) {
                                    //validar que el valor no sea null
                                    if ($valueItems != null) {
                                        //comprobar si el value es un array, si es convertirlo a object mas facil para manejar.
                                        if (is_array($valueItems))
                                            $valueItems = (object)$valueItems;

                                        $data->servidor_id = env("SERVIDOR_ID");
                                        $data->clues                = $datos->clues;
                                        $data->turnos_id            = $datos->turnos_id;
                                        $data->usuarios_id          = $datos->usuarios_id;
                                        $data->cartera_servicios_id = $valueItems->cartera_servicios_id;
                                        $data->items_id             = $valueItems->tipos_items_id;
                                        $data->respuesta            = $valueItems->respuesta;

                                        if ($data->save()) {
                                            $success = true;
                                        }

                                    }
                                }

                            }
                        }

                    }
                }
        //}

        return $success;
    }


}
