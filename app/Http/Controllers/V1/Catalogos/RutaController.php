<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

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
     * Apoyos ordenamiento con respecto a id:
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
                $data = Rutas::orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where('id','LIKE',"%".$keyword['q']."%")
                        ->orWhere('nombre','LIKE',"%".$keyword['q']."%")
                        ->orWhere('descripcion','LIKE',"%".$keyword['q']."%");
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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