<?php

namespace App\Http\Controllers\V1\Catalogos;

use App\Http\Requests;
use App\Models\Catalogos\TiposAltas;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use App\Http\Controllers\ApiController;
use App\Models\Catalogos\Apoyos;


/**
 * Controlador TipoAlta
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `TipoAlta`: Controlador  para el manejo de catalogo tipos altas
 *
 */
class TipoAltaController extends ApiController
{
    /**
     * @api {get} /tipos-altas 1.Listar tipos altas
     * @apiVersion 1.0.0
     * @apiName GetTipoAlta
     * @apiGroup Catalogo/TipoAltaController
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

        // Si existe el parametro pagina en la url devolver las filas según sea el caso
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
                $data = TiposAltas::orderBy($order,$orden);

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
                $data = TiposAltas::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = TiposAltas::all();
            }

        }
        else{
            $data = TiposAltas::get();
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
     * @api {post} /tipos-altas 2.Crea nuevo apoyo
     * @apiVersion 1.0.0
     * @apiName PostTipoAlta
     * @apiGroup Catalogo/TipoAltaController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo tipo alta.
     *
     * @apiParam {String} nombre Nombre del Tipo Alta.
     * @apiParam {String} descripcion Descripcion del Tipo Alta.
     *
     * @apiSuccess {String} id         informacion del nuevo Tipo Alta.
     *
     */
    public function store(Request $request)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'nombre'        => 'required|unique:tipos-altas',
        ];

        $inputs = Input::only('nombre', 'descripcion');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = TiposAltas::create($inputs);
            return $this->respuestaVerUno($data,201);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {get} /tipos-altas/:id 3.Consulta datos de un Tipo Alta
     * @apiVersion 1.0.0
     * @apiName ShowTipoAlta
     * @apiGroup Catalogo/TipoAltaController
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
        $data = TiposAltas::find($id);

        if(!$data){
            return $this->respuestaError('No se encuentra el recurso que esta buscando.', 404);
        }

        return $this->respuestaVerUno($data);
    }

    /**
     * @api {put} /tipos-altas/:id 4.Actualiza Tipo Alta
     * @apiVersion 1.0.0
     * @apiName PutTipoAlta
     * @apiGroup Catalogo/TipoAltaController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Tipo Alta.
     *
     * @apiParam {number} id del Tipo Alta que se quiere editar.
     * @apiParam {String} nombre Nombre del Tipo Alta.
     * @apiParam {String} descripcion Descripcion del Tipo Alta.
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
            $data = TiposAltas::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->descripcion =  $inputs['descripcion'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {destroy} /tipos-altas/:id 5.Elimina Tipo Alta
     * @apiVersion 1.0.0
     * @apiName DestroyTipoAlta
     * @apiGroup Catalogo/TipoAltaController
     * @apiPermission Admin
     *
     * @apiDescription Elimina un Tipo Alta.
     *
     * @apiParam {number} id del Tipo Alta que se quiere editar.
     **
     */
    public function destroy($id)
    {
        try {
            $data = TiposAltas::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
