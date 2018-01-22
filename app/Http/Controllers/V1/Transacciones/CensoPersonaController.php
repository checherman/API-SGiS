<?php

namespace App\Http\Controllers\V1\Transacciones;

use App\Http\Controllers\ApiController;
use App\Models\Transacciones\Acompaniantes;
use Illuminate\Support\Facades\Request;
use App\Models\Transacciones\Personas;
use Illuminate\Http\Response as HttpResponse;

use App\Http\Requests;

use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

/**
 * Controlador CensoPersona
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `CensoPersona`: Controlador  para el manejo de censo de personas
 *
 */
class CensoPersonaController extends ApiController
{
    /**
     * @api {get} /censo-personas 1.Listar censo personas
     * @apiVersion 1.0.0
     * @apiName GetCensoPersona
     * @apiGroup Transaccion/CensoPersonaController
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
        $acompaniantes = Acompaniantes::select('personas_id')->get();
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
                $data = Personas::whereNotIn('id', $acompaniantes)->with('derechohabientes','estados_embarazos','localidades','municipios')->orderBy($order,$orden);

                $search = trim($valor);
                $keyword = $search;
                $data = $data->whereNested(function($query) use ($keyword){
                    $query->where("id", "LIKE", '%'.$keyword.'%')
                        ->orWhere("nombre", "LIKE", '%'.$keyword.'%')
                        ->orWhere("paterno", "LIKE", '%'.$keyword.'%')
                        ->orWhere("materno", "LIKE", '%'.$keyword.'%')
                        ->orWhere("telefono", "LIKE", '%'.$keyword.'%')
                        ->orWhere("domicilio", "LIKE", '%'.$keyword.'%');
                });

                $total = $data->get();
                $data = $data->skip($pagina-1)->take($datos['limite'])->get();
            }
            else{
                $data = Personas::whereNotIn('id', $acompaniantes)->with('derechohabientes','estados_embarazos','localidades','municipios')->skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
                $total = Personas::all();
            }

        }
        else{
            $data = Personas::whereNotIn('id', $acompaniantes)->with('derechohabientes','estados_embarazos','localidades','municipios')->get();
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
     * @api {post} /censo-personas 2.Crea nueva Persona
     * @apiVersion 1.0.0
     * @apiName PostCensoPersona
     * @apiGroup Transaccion/CensoPersonaController
     * @apiPermission Admin
     *
     * @apiDescription Crea una nueva Persona.
     *
     * @apiParam {String} nombre Nombre.
     * @apiParam {String} paterno Apellido Paterno.
     * @apiParam {String} materno Apellido Materno.
     * @apiParam {String} telefono Telefono.
     * @apiParam {String} domicilio Domicilio.
     *
     * @apiSuccess {String} id         informacion del nuevo apoyo.
     *
     */
    public function store(Request $request)
    {
        $mensajes = [
            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'id'            => 'required|unique:personas',
            'nombre'        => 'required',
            'paterno'       => 'required',
            'materno'       => 'required',
            'telefono'      => 'required',
            'domicilio'     => 'required',

        ];

        $inputs = Input::only('id','nombre','paterno','materno','fecha_nacimiento','telefono','domicilio','estados_embarazos_id','derechohabientes_id','municipios_id','localidades_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = Personas::create($inputs);

            return Response::json([ 'data' => $data ],200);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {get} /censo-personas/:id 3.Consulta datos de una Persona
     * @apiVersion 1.0.0
     * @apiName ShowCensoPersona
     * @apiGroup Transaccion/CensoPersonaController
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
        $data = Personas::where('id',$id)->with('municipios','localidades','derechohabientes', 'estados_embarazos')->first();
        if(!$data){
            return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
        }

        return Response::json(['data' => $data], HttpResponse::HTTP_OK);
    }

    /**
     * @api {put} /censo-personas/:id 4.Actualiza apoyo
     * @apiVersion 1.0.0
     * @apiName PutCensoPersona
     * @apiGroup Transaccion/CensoPersonaController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza una Persona.
     *
     * @apiParam {number} id de la Persona que se quiere editar.
     * @apiParam {String} nombre Nombre.
     * @apiParam {String} paterno Apellido Paterno.
     * @apiParam {String} materno Apellido Materno.
     * @apiParam {String} telefono Telefono.
     * @apiParam {String} domicilio Domicilio.
     **
     */
    public function update(Request $request, $id)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'id'        => 'required',
            'nombre'    => 'required',
            'paterno'   => 'required',
            'materno'   => 'required',
            'telefono'  => 'required',
            'domicilio' => 'required',
        ];

        $inputs = Input::only('id','nombre','paterno','materno','fecha_nacimiento','telefono','domicilio','estados_embarazos_id','derechohabientes_id','municipios_id','localidades_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = Personas::find($id);
            $data->nombre =  $inputs['nombre'];
            $data->paterno =  $inputs['paterno'];
            $data->materno =  $inputs['materno'];
            $data->fecha_nacimiento =  $inputs['fecha_nacimiento'];
            $data->telefono =  $inputs['telefono'];
            $data->domicilio =  $inputs['domicilio'];
            $data->estados_embarazos_id =  $inputs['estados_embarazos_id'];
            $data->derechohabientes_id =  $inputs['derechohabientes_id'];
            $data->municipios_id =  $inputs['municipios_id'];
            $data->localidades_id =  $inputs['localidades_id'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {destroy} /censo-personas/:id 5.Elimina Persona
     * @apiVersion 1.0.0
     * @apiName DestroyCensoPersona
     * @apiGroup Transaccion/CensoPersonaController
     * @apiPermission Admin
     *
     * @apiDescription Elimina una Persona.
     *
     * @apiParam {number} id de la Persona que se quiere editar.
     **
     */
    public function destroy($id)
    {
        try {
            $data = Personas::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
