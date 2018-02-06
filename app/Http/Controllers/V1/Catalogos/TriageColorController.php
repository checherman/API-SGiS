<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use App\Http\Controllers\ApiController;
use App\Models\Catalogos\TriageColores;

/**
 * Controlador TriageColor
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-06-12
 *
 * Controlador `TriageColor`: Controlador  para el manejo de catalogo colores del triage
 *
 */
class TriageColorController extends ApiController
{
    /**
     * @api {get} /triage-colores 1.Listar triage colores
     * @apiVersion 1.0.0
     * @apiName GetTriageColor
     * @apiGroup Catalogo/TriageColorController
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
                $data = TriageColores::orderBy($order,$orden);

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
                $data = TriageColores::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = TriageColores::all();
            }

        }
        else{
            $data = TriageColores::get();
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
     * @api {post} /triage-colores 2.Crea nuevo apoyo
     * @apiVersion 1.0.0
     * @apiName PostTriageColor
     * @apiGroup Catalogo/TriageColorController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo Triage Color.
     *
     * @apiParam {String} nombre Nombre del Triage Color.
     * @apiParam {String} tiempo_minimo Tiempo minimo de atención.
     * @apiParam {String} tiempo_maximo Tiempo maximo de atención.
     *
     * @apiSuccess {String} id         informacion del nuevo Triage Color.
     *
     */
    public function store(Request $request)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required|unique:triage_colores',
            'tiempo_minimo' => 'required',
            'tiempo_maximo' => 'required',
        ];

        $inputs = Input::only('nombre', 'descripcion', 'tiempo_minimo', 'tiempo_maximo');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = TriageColores::create($inputs);

            return $this->respuestaVerUno($data,201);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {get} /triage-colores/:id 3.Consulta datos de un Triage Color
     * @apiVersion 1.0.0
     * @apiName ShowTriageColor
     * @apiGroup Catalogo/TriageColorController
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
        $data = TriageColores::find($id);

        if(!$data){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->respuestaVerUno($data);
    }

    /**
     * @api {put} /triage-colores/:id 4.Actualiza Triage Color
     * @apiVersion 1.0.0
     * @apiName PutTriageColor
     * @apiGroup Catalogo/TriageColorController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Triage Color.
     *
     * @apiParam {number} id del Triage Color que se quiere editar.
     * @apiParam {String} nombre Nombre del Triage Color.
     * @apiParam {String} tiempo_minimo Tiempo minimo de atención.
     * @apiParam {String} tiempo_maximo Tiempo maximo de atención.
     **
     */
    public function update(Request $request, $id)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required',
            'tiempo_minimo' => 'required',
            'tiempo_maximo' => 'required',
        ];

        $inputs = Input::only('nombre', 'descripcion', 'tiempo_minimo', 'tiempo_maximo');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = TriageColores::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->descripcion =  $inputs['descripcion'];
            $data->tiempo_minimo =  $inputs['tiempo_minimo'];
            $data->tiempo_maximo =  $inputs['tiempo_maximo'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {destroy} /triage-colores/:id 5.Elimina apoyo
     * @apiVersion 1.0.0
     * @apiName DestroyTriageColor
     * @apiGroup Catalogo/TriageColorController
     * @apiPermission Admin
     *
     * @apiDescription Elimina un Triage Color.
     *
     * @apiParam {number} id del Triage Color que se quiere editar.
     **
     */
    public function destroy($id)
    {
        try {
            $data = TriageColores::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
