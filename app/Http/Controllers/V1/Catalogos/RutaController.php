<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use Illuminate\Http\Response as HttpResponse;


use App\Http\Controllers\Controller;
use App\Models\Catalogos\Rutas;
/**
 * Controlador Ruta
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Ruta`: Controlador  para el manejo de catalogo de las rutas
 *
 */
class RutaController extends Controller
{
    /**
     * @api {get} /rutas 1.Listar rutas
     * @apiVersion 1.0.0
     * @apiName GetRutas
     * @apiGroup Catalogo/RutaController
     *
     * @apiDescription Muestra una lista de los recurso según los parametros a procesar en la petición
     *
     * @apiPermission Admin
     *
     * @apiParam {Number} pagina Numero del puntero(offset) para la sentencia limit.
     * @apiParam {Number} limite Numero de filas a mostrar por página.
     * @apiParam {Boolean} buscar Mandar por defecto true, para realizar la busqueda.
     *
     * @apiParam {String} valor Valor para hacer la busqueda.
     * @apiParam {String} order Campo de la base de datos por la que se debe ordenar la información. Por Default es ASC, pero si se antepone el signo - es de manera DESC.
     *
     * @apiParamExample {json} Ordenamiento - Ejemplo:
    http://url?pagina=1&limite=5&order=id ASC
    http://url?pagina=1&limite=5&order=id DESC
    Todo Los parametros son opcionales, pero si existe pagina debe de existir tambien limite
     *
     * @apiParamExample {json} Busqueda - Ejemplo:
    http://url?valor=busqueda&buscar=true
     *
     * @apiSuccess {Object[]} data Lista.
     * @apiSuccess {String} messages Mensaje de Operación realizada con exito.
     * @apiSuccess {Number} status Estatus 200.
     * @apiSuccess {Number} total Total de datos devueltos.
     *
     * @apiSuccessExample Respuesta exitosa:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [{},{}...],
     *       "messages": "Operación realizada con exito",
     *       "status": 200,
     *       "total": TotalDeDatosDevueltos
     *     }
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
                $data = Rutas::orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where('id','LIKE',"%".$keyword."%")
                        ->orWhere('nombre','LIKE',"%".$keyword."%")
                        ->orWhere('descripcion','LIKE',"%".$keyword."%");
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = Rutas::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = Rutas::all();
            }

        }
        else{
            $data = Rutas::get();
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
     * @api {post} /rutas 2.Crea nuevo rutas
     * @apiVersion 1.0.0
     * @apiName PostRuta
     * @apiGroup Catalogo/RutaController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo Ruta.
     *
     * @apiParam {String} nombre Nombre del Ruta.
     * @apiParam {String} clues_origen Unidad medica de Origen.
     * @apiParam {String} clues_destino Unidad medica de Destino.
     * @apiParam {String} tiempo_traslado Tiempo de traslaoo entre unidades medicas.
     * @apiParam {String} distancia_traslado Distancia de traslaoo entre unidades medicas.
     * @apiParam {String} observaciones Observaciones del Ruta.
     * @apiParam {String} numeroLatitud_origen Latitud de Unidad medica de Origen.
     * @apiParam {String} numeroLongitud_origen Longitud de Unidad medica de Origen.
     * @apiParam {String} numeroLatitud_destino Latitud de Unidad medica de Destino.
     * @apiParam {String} numeroLongitud_destino Longitud de Unidad medica de Destino.
     *
     * @apiSuccess {String} id         informacion del nuevo Ruta.
     *
     */
    public function store(Request $request)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'                => 'required|unique:rutas',
            'clues_origen'          => 'required',
            'clues_destino'         => 'required',
            'tiempo_traslado'       => 'required',
            'distancia_traslado'    => 'required',
            'observaciones'         => 'required',
            'numeroLatitud_origen'         => 'required',
            'numeroLongitud_origen'         => 'required',
            'numeroLatitud_destino'         => 'required',
            'numeroLongitud_destino'         => 'required'
        ];

        $inputs = Input::only('nombre', 'clues_origen', 'clues_destino', 'tiempo_traslado', 'distancia_traslado', 'observaciones', 'numeroLatitud_origen', 'numeroLongitud_origen', 'numeroLatitud_destino', 'numeroLongitud_destino');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $data = Rutas::create($inputs);

            return Response::json([ 'data' => $data ],200);

        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * @api {get} /rutas/:id 3.Consulta datos de un apoyo
     * @apiVersion 1.0.0
     * @apiName ShowRutas
     * @apiGroup Catalogo/RutaController
     *
     * @apiDescription Muestra una lista de los recurso según los parametros a procesar en la petición
     *
     * @apiPermission Admin
     *
     * @apiParamExample {json} Ejemplo de uso:
    http://url/1
     *
     * @apiSuccess {Object[]} data Lista.
     * @apiSuccess {String} messages Mensaje de Operación realizada con exito.
     * @apiSuccess {Number} status Estatus 200.
     * @apiSuccess {Number} total Total de datos devueltos.
     *
     * @apiSuccessExample Respuesta exitosa:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [{},{}...],
     *       "messages": "Operación realizada con exito",
     *       "status": 200,
     *       "total": TotalDeDatosDevueltos
     *     }
     */
    public function show($id)
    {
        $data = Rutas::find($id);

        if(!$data){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return Response::json([ 'data' => $data ], HttpResponse::HTTP_OK);
    }

    /**
     * @api {put} /rutas/:id 4.Actualiza apoyo
     * @apiVersion 1.0.0
     * @apiName PutRuta
     * @apiGroup Catalogo/RutaController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Ruta.
     *
     * @apiParam {number} id del Ruta que se quiere editar.
     * @apiParam {String} nombre Nombre del Ruta.
     * @apiParam {String} clues_origen Unidad medica de Origen.
     * @apiParam {String} clues_destino Unidad medica de Destino.
     * @apiParam {String} tiempo_traslado Tiempo de traslaoo entre unidades medicas.
     * @apiParam {String} distancia_traslado Distancia de traslaoo entre unidades medicas.
     * @apiParam {String} observaciones Observaciones del Ruta.
     * @apiParam {String} numeroLatitud_origen Latitud de Unidad medica de Origen.
     * @apiParam {String} numeroLongitud_origen Longitud de Unidad medica de Origen.
     * @apiParam {String} numeroLatitud_destino Latitud de Unidad medica de Destino.
     * @apiParam {String} numeroLongitud_destino Longitud de Unidad medica de Destino.
     **
     */
    public function update(Request $request, $id)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'                => 'required',
            'clues_origen'          => 'required',
            'clues_destino'         => 'required',
            'tiempo_traslado'       => 'required',
            'distancia_traslado'    => 'required',
            'observaciones'         => 'required',
            'numeroLatitud_origen'         => 'required',
            'numeroLongitud_origen'         => 'required',
            'numeroLatitud_destino'         => 'required',
            'numeroLongitud_destino'         => 'required'
        ];

        $inputs = Input::only('nombre', 'clues_origen', 'clues_destino', 'tiempo_traslado', 'distancia_traslado', 'observaciones', 'numeroLatitud_origen', 'numeroLongitud_origen', 'numeroLatitud_destino', 'numeroLongitud_destino');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return Response::json(['error' => $v->errors()], HttpResponse::HTTP_CONFLICT);
        }

        try {
            $data = Rutas::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->clues_origen =  $inputs['clues_origen'];
            $data->clues_destino =  $inputs['clues_destino'];
            $data->tiempo_traslado =  $inputs['tiempo_traslado'];
            $data->distancia_traslado =  $inputs['distancia_traslado'];
            $data->observaciones =  $inputs['observaciones'];
            $data->numeroLatitud_origen =  $inputs['numeroLatitud_origen'];
            $data->numeroLongitud_origen =  $inputs['numeroLongitud_origen'];
            $data->numeroLatitud_destino =  $inputs['numeroLatitud_destino'];
            $data->numeroLongitud_destino =  $inputs['numeroLongitud_destino'];

            $data->save();
            return Response::json([ 'data' => $data ],200);

        } catch (\Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }

    /**
     * @api {destroy} /rutas/:id 5.Elimina apoyo
     * @apiVersion 1.0.0
     * @apiName DestroyRuta
     * @apiGroup Catalogo/RutaController
     * @apiPermission Admin
     *
     * @apiDescription Elimina un Ruta.
     *
     * @apiParam {number} id del Ruta que se quiere editar.
     **
     */
    public function destroy($id)
    {
        try {
            $data = Rutas::destroy($id);
            return Response::json(['data'=>$data],200);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], HttpResponse::HTTP_CONFLICT);
        }
    }
}