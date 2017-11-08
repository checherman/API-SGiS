<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Catalogos\CarteraServicios;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\NivelesCones;
use App\Models\Transacciones\EstadosFuerza;
use App\Models\Transacciones\RespuestasEstadosFuerza;
use Illuminate\Http\Response as HttpResponse;

use Request;
use \Validator, \Hash, \Response, \DB;
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
     * Muestra una lista de los recurso según los parametros a procesar en la petición.
     *
     * <h3>Lista de parametros Request:</h3>
     * <Ul>Paginación
     * <Li> <code>$pagina</code> numero del puntero(offset) para la sentencia limit </ li>
     * <Li> <code>$limite</code> numero de filas a mostrar por página</ li>
     * </Ul>
     * <Ul>Busqueda
     * <Li> <code>$valor</code> string con el valor para hacer la busqueda</ li>
     * <Li> <code>$order</code> campo de la base de datos por la que se debe ordenar la información. Por Defaul es ASC, pero si se antepone el signo - es de manera DESC</ li>
     * </Ul>
     *
     * Clues ordenamiento con respecto a clues:
     * <code>
     * http://url?pagina=1&limite=5&order=id ASC
     * </code>
     * <code>
     * http://url?pagina=1&limite=5&order=-id DESC
     * </code>
     *
     * Todo Los parametros son opcionales, pero si existe pagina debe de existir tambien limite
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function index(){
        $datos = Request::all();

        // Si existe el paarametro pagina en la url devolver las filas según sea el caso
        // si no existe parametros en la url devolver todos las filas de la tabla correspondiente
        // esta opción es para devolver todos los datos cuando la tabla es de tipo catálogo
        if(array_key_exists('pagina', $datos)){
            $pagina = $datos['pagina'];
            if(isset($datos['order'])){
                $order = $datos['order'];
                if(strpos(" ".$order,"-"))
                    $orden = "desc";
                else
                    $orden = "asc";
                $order = str_replace("-", "", $order);
            }
            else{
                $order = "id"; $orden = "asc";
            }

            if($pagina == 0){
                $pagina = 1;
            }
            if($pagina == 1)
                $datos["limite"] = $datos["limite"] - 1;
            // si existe buscar se realiza esta linea para devolver las filas que en el campo que coincidan con el valor que el usuario escribio
            // si no existe buscar devolver las filas con el limite y la pagina correspondiente a la paginación
            if(array_key_exists('buscar', $datos)){
                $columna = $datos['columna'];
                $valor   = $datos['valor'];
                $data = EstadosFuerza::with('clues', 'respuesta_estados_fuerza', 'turnos', 'sis_usuarios')->orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where("id", "LIKE", '%'.$keyword.'%')
                        ->orWhere("nombre", "LIKE", '%'.$keyword.'%');
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = EstadosFuerza::with('clues', 'respuesta_estados_fuerza', 'turnos', 'sis_usuarios')->skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = EstadosFuerza::all();
            }

        }
        else{
            $data = EstadosFuerza::with('clues', 'respuesta_estados_fuerza', 'turnos', 'sis_usuarios')->get();
            $total = $data;
        }

        if(!$data){
            return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
        }
        else{
            return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data,"total" => count($total)), 200);

        }
    }


    /**
     * Actualizar el  registro especificado en el la base de datos
     *
     * <h4>Request</h4>
     * Recibe un Input Request con el json de los datos
     *
     * @param  int $id que corresponde al identificador del dato a actualizar
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 304, "messages": "No modificado"),status) </code>
     */
    public function update($id)
    {
        $datos = Request::json()->all();

        DB::beginTransaction();

        try {

            $data = new EstadosFuerza;
            $success = $this->AgregarDatosRespuesta($datos, $data);

        } catch (\Exception $e) {
            return Response::json($e->getMessage(), 500);
        }

        if ($success) {
            DB::commit();
            return Response::json(array("status" => 201, "messages" => "Creado", "data" => $data), 201);
        } else {
            DB::rollback();
            return Response::json(array("status" => 409, "messages" => "Conflicto"), 409);
        }
    }

    /**
     * Devuelve la información del registro especificado.
     *
     * @param  int $id que corresponde al identificador del recurso a mostrar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
     */
    public function show($id)
    {
        $clue = Request::header('clues');

        $nivelCONE = Clues::select("nivel_cone_id")->where('clues', $clue)->first();
        $nivelesCones = NivelesCones::find($nivelCONE->nivel_cone_id);

        if (!$nivelesCones) {

            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        $ahora = date("Y-m-d h:i:s");
        $data = collect();
        $data->put('clues', "");
        $data->put('sis_usuarios_id', "");
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

        return Response::json(['data' => $data], HttpResponse::HTTP_OK);
    }

    private function AgregarDatosRespuesta($datos, $data)
    {
        $success = false;
        $data->clues = $datos['clues'];
        $data->turnos_id = $datos['turnos_id'];
        $data->sis_usuarios_id = $datos['sis_usuarios_id'];

        if ($data->save()) {
            $datos = (object) $datos;

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

                                    $respuestaEstadosFuerza = new RespuestasEstadosFuerza;

                                    $respuestaEstadosFuerza->estados_fuerza_id = $data->id;
                                    $respuestaEstadosFuerza->cartera_servicios_id = $valueItems->cartera_servicios_id;
                                    $respuestaEstadosFuerza->items_id = $valueItems->tipos_items_id;

                                    if(!$valueItems->respuesta == null){
                                        $respuestaEstadosFuerza->respuesta = $valueItems->respuesta;
                                    }else
                                        $respuestaEstadosFuerza->respuesta = false;

                                    if ($respuestaEstadosFuerza->save()) {
                                        $success = true;
                                    }

                                }
                            }

                        }
                    }

                }
            }
        }


        return $success;
    }


}
