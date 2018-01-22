<?php

namespace App\Http\Controllers\V1\Catalogos;


use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use \Validator,\Hash, \Response;

use App\Http\Controllers\ApiController;
use App\Models\Catalogos\Municipios;


/**
 * Controlador Municipio
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Municipio`: Controlador  para el manejo de catalogo Municipios
 *
 */
class MunicipioController extends ApiController
{
    /**
     * @api {get} /municipios 1.Listar municipios
     * @apiVersion 1.0.0
     * @apiName GetMunicipio
     * @apiGroup Catalogo/MunicipioController
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
    public function index()
    {
        $parametros = Input::only('q','page','per_page');
        if ($parametros['q']) {
            $data =  Municipios::with('jurisdicciones')->where(function($query) use ($parametros) {
                $query->where('id','LIKE',"%".$parametros['q']."%")
                    ->orWhere('nombre','LIKE',"%".$parametros['q']."%")
                    ->orWhere('clave','LIKE',"%".$parametros['q']."%");
            });
        } else {
            $data =  Municipios::with('jurisdicciones');
        }

        if(isset($parametros['page'])){

            $resultadosPorPagina = isset($parametros["per_page"]) ? $parametros["per_page"] : 20;
            $data = $data->paginate($resultadosPorPagina);
        } else {
            $data = $data->get();
        }

        return $this->respuestaVerTodo($data);
    }

    /**
     * @api {post} /municipios 2.Crea nuevo municipio
     * @apiVersion 1.0.0
     * @apiName PostMunicipio
     * @apiGroup Catalogo/MunicipioController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo Municipio.
     *
     * @apiParam {number} id                 id del Municipio que se quiere editar.
     * @apiParam {String} clave              clave del Municipio.
     * @apiParam {String} nombre             Nombre del Municipio.
     * @apiParam {number} jurisdicciones_id  jurisdiccion a la que pertenece el Municipio.
     * @apiParam {number} entidades_id       entidad a la que pertenece el Municipio.
     *
     * @apiSuccess {String} id         informacion del nuevo Municipio.
     *
     */
    public function store(Request $request)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'clave'         => 'required',
            'nombre'        => 'required|unique:municipios',
        ];

        $inputs = Input::only('clave', 'nombre', 'jurisdicciones_id', 'entidades_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {

            $data = Municipios::create($inputs);
            return $this->respuestaVerUno($data,201);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {put} /municipios/:id 4.Actualiza Municipio
     * @apiVersion 1.0.0
     * @apiName PutMunicipio
     * @apiGroup Catalogo/MunicipioController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Municipio.
     *
     * @apiParam {number} id                 id del Municipio que se quiere editar.
     * @apiParam {String} clave              clave del Municipio.
     * @apiParam {String} nombre             Nombre del Municipio.
     * @apiParam {number} jurisdicciones_id  jurisdiccion a la que pertenece el Municipio.
     * @apiParam {number} entidades_id       entidad a la que pertenece el Municipio.
     **
     */
    public function update(Request $request, $id)
    {
        $mensajes = [

            'required'      => "required",
            'unique'        => "unique"
        ];

        $reglas = [
            'clave'         => 'required',
            'nombre'        => 'required',
        ];

        $inputs = Input::only('clave', 'nombre', 'jurisdicciones_id','entidades_id');

        $v = Validator::make($inputs, $reglas, $mensajes);

        if ($v->fails()) {
            return $this->respuestaError($v->errors(), 409);
        }

        try {
            $data = Municipios::find($id);
            $data->clave =  $inputs['clave'];
            $data->nombre =  $inputs['nombre'];
            $data->entidades_id =  $inputs['entidades_id'];
            $data->jurisdicciones_id =  $inputs['jurisdicciones_id'];

            $data->save();
            return $this->respuestaVerUno($data);

        } catch (\Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }

    /**
     * @api {get} /municipios/:id 3.Consulta datos de un Municipio
     * @apiVersion 1.0.0
     * @apiName ShowMunicipio
     * @apiGroup Catalogo/MunicipioController
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
        $data = Municipios::find($id);

        if(!$data){
            return $this->respuestaError('No se encuentra el recurso que esta buscando.', 404);
        }

        return $this->respuestaVerTodo($data);
    }

    /**
     * @api {destroy} /municipios/:id 5.Elimina Municipio
     * @apiVersion 1.0.0
     * @apiName DestroyMunicipio
     * @apiGroup Catalogo/MunicipioController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Municipio.
     *
     * @apiParam {number} id del Municipio que se quiere editar.
     **
     */
    public function destroy($id)
    {
        try {
            $data = Municipios::destroy($id);
            return $this->respuestaVerTodo($data);
        } catch (Exception $e) {
            return $this->respuestaError($e->getMessage(), 409);
        }
    }
}
