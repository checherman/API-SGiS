<?php

namespace App\Http\Controllers\v1\Catalogos;

use App\Http\Requests;
use App\Models\Catalogos\TiposNotificaciones;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use App\Http\Controllers\ApiController;
/**
 * Controlador TipoNotificaciones
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `TipoNotificaciones`: Controlador  para el manejo de catalogo de tipos de notificaciones
 *
 */
class TipoNotificacionController extends ApiController
{
    /**
     * @api {get} /tipos-notificaciones 1.Listar tipos notificaciones
     * @apiVersion 1.0.0
     * @apiName GetTipoNotificacion
     * @apiGroup Catalogo/TipoNotificacionController
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
                $data = TiposNotificaciones::orderBy($order,$orden);

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
                $data = TiposNotificaciones::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = TiposNotificaciones::all();
            }

        }
        else{
            $data = TiposNotificaciones::get();
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
     * @api {post} /tipos-notificaciones 2.Crea nuevo Notificacion
     * @apiVersion 1.0.0
     * @apiName PostTipoNotificacion
     * @apiGroup Catalogo/TipoNotificacionController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo tipo Notificacion.
     *
     * @apiParam {String} nombre Nombre del Tipo Notificacion.
     * @apiParam {String} descripcion Descripcion del Tipo Notificacion.
     *
     * @apiSuccess {String} id         informacion del nuevo Tipo Notificacion.
     *
     */
    public function store(Request $request)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required|unique:apoyos',
        ];

        $inputs = Input::only('nombre', 'descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = TiposNotificaciones::create($inputs);
            return $this->respuestaVerUno($data,201);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {get} /tipos-notificaciones/:id 3.Consulta datos de un Tipo Notificacion
     * @apiVersion 1.0.0
     * @apiName ShowTipoNotificacion
     * @apiGroup Catalogo/TipoNotificacionController
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
        $data = TiposNotificaciones::find($id);

        if(!$data){
            return $this->respuestaError('No se encuentra el recurso que esta buscando.', 404);
        }

        return $this->respuestaVerUno($data);
    }

    /**
     * @api {put} /tipos-notificaciones/:id 4.Actualiza Tipo Notificacion
     * @apiVersion 1.0.0
     * @apiName PutTipoNotificacion
     * @apiGroup Catalogo/TipoNotificacionController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Tipo Notificacion.
     *
     * @apiParam {number} id del Tipo Notificacion que se quiere editar.
     * @apiParam {String} nombre Nombre del Tipo Notificacion.
     * @apiParam {String} descripcion Descripcion del Tipo Notificacion.
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
        ];

        $inputs = Input::only('nombre','descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = TiposNotificaciones::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->descripcion =  $inputs['descripcion'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {destroy} /tipos-notificaciones/:id 5.Elimina Tipo Notificacion
     * @apiVersion 1.0.0
     * @apiName DestroyTipoNotificacion
     * @apiGroup Catalogo/TipoNotificacionController
     * @apiPermission Admin
     *
     * @apiDescription Elimina un Tipo Notificacion.
     *
     * @apiParam {number} id del Tipo Notificacion que se quiere editar.
     **
     */
    public function destroy($id)
    {
        try {
            $data = TiposNotificaciones::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
