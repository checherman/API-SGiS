<?php
namespace App\Http\Controllers\v1\Catalogos;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Input;
use DB; 
use App\Models\Catalogos\Estados;
/**
 * Controlador Estado
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `Estado`: Controlador  para el manejo de Estados
 *
 */
class EstadoController extends Controller {

    /**
     * @api {get} /estados 1.Listar estados
     * @apiVersion 1.0.0
     * @apiName GetEstados
     * @apiGroup Catalogo/EstadoController
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
				$data = Estados::with("Paises")
				->selectRaw("paises.id as idPais,paises.nombre as pais,estados.id, estados.clave, estados.nombre, estados.updated_at,estados.updated_at,estados.created_at")
				->leftJoin('paises', 'paises.id', '=', 'estados.paises_id')				
				->orderBy($order,$orden);
				
				$search = trim($valor);
				$keyword = $search;
				$data = $data->whereNested(function($query) use ($keyword){					
					$query->Where('estados.nombre', 'LIKE', '%'.$keyword.'%')
					->orWhere('paises.nombre', 'LIKE', '%'.$keyword.'%')
					->orWhere('paises.id', $keyword); 
				});
				
				$total = $data->get();
				$data = $data->skip($pagina-1)->take($datos['limite'])->get();
			}
			else{
				$data = Estados::with("Paises")
				->selectRaw("paises.id as idPais,paises.nombre as pais,estados.id, estados.clave, estados.nombre, estados.updated_at,estados.updated_at,estados.created_at")
				->leftJoin('paises', 'paises.id', '=', 'estados.paises_id')
				->skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();

				$total = Estados::all();
			}
			
		}
		else{
			$data = Estados::with("Paises")
				->selectRaw("paises.id as idPais,paises.nombre as pais,estados.id, estados.clave, estados.nombre, estados.updated_at,estados.updated_at,estados.created_at")
				->leftJoin('paises', 'paises.id', '=', 'estados.paises_id');
			
			if(isset($datos["id"]))
				$data = $data->where("paises.nombre", $datos["id"])->orWhere("paises.id", $datos["id"]);
			$data = $data->get();
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
     * @api {post} /estados 2.Crea nuevo estado
     * @apiVersion 1.0.0
     * @apiName PostEstado
     * @apiGroup Catalogo/EstadoController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo estado.
     *
     * @apiParam {String} nombre Nombre del Estado.
     * @apiParam {number} paises_id Pais al que pertenece el Estado.
     *
     * @apiSuccess {String} id         informacion del nuevo estado.
     *
     */
	public function store(){
		$v = $this->ValidarParametros(Request::json()->all());
		dd($v);
		$datos = Input::json();
		$success = false;
		
        DB::beginTransaction();
        try{
        		
            $data = new Estados;
            $data->nombre = $datos->get('nombre');
            $data->paises_id = $datos->get('paises_id');

            if ($data->save()) 
                $success = true;
        } 
		catch (\Exception $e){
			return Response::json($e->getMessage(), 500);
        }
        if ($success){
            DB::commit();
			return Response::json(array("status" => 201,"messages" => "Creado","data" => $data), 201);
        } 
		else{
            DB::rollback();
			return Response::json(array("status" => 409,"messages" => "Conflicto"), 409);
        }
		
	}

    /**
     * @api {get} /estados/:id 3.Consulta datos de un estado
     * @apiVersion 1.0.0
     * @apiName ShowEstado
     * @apiGroup Catalogo/EstadoController
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
	public function show($id){
		$data = Estados::find($id);

		if(!$data){
			return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
		} 
		else{
			return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data), 200);
		}
	}

    /**
     * @api {put} /estados/:id 4.Actualiza estado
     * @apiVersion 1.0.0
     * @apiName PutEstado
     * @apiGroup Catalogo/EstadoController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un estado.
     *
     * @apiParam {number} id del estado que se quiere editar.
     * @apiParam {String} nombre Nombre del Estado.
     * @apiParam {number} paises_id Pais al que pertenece el Estado.
     **
     */
	public function update($id){
		$this->ValidarParametros(Request::json()->all());	
	
		$datos = Request::json(); 
		$success = false;
        DB::beginTransaction();
        try{
			$data = Estados::find($id);
				
            $data->nombre = $datos->get('nombre');
            $data->paises_id = $datos->get('paises_id');

            if ($data->save()) 
                $success = true;
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
     * @api {destroy} /estados/:id 5.Elimina estado
     * @apiVersion 1.0.0
     * @apiName DestroyEstado
     * @apiGroup Catalogo/EstadoController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un estado.
     *
     * @apiParam {number} id del estado que se quiere editar.
     **
     */
	public function destroy($id)
	{
		$success = false;
        DB::beginTransaction();
        try {
			$data = Estados::find($id);
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
     * @api /estados 6.ValidarParametros
     * @apiVersion 1.0.0
     * @apiName EstadoValidarParametros
     * @apiGroup Catalogo/EstadoController
     * @apiPermission Admin
     *
     * @apiDescription Metodo que valida los parametros.
     *
     * @apiParam {json} request datos del request a validar.
     * @apiParamExample {json} Request-Ejemplo:
     *     {
     *        "data": {
     *           "nombre": "Nombre",
     *           "descripcion": "Descripcion",
     *           "id": id
     *        }
     *     }
     *
     * @apiSuccess {json} data datos del objeto que se va a crear.
     * @apiSuccessExample {json} Success-Response:
     *     {
     *        "data": {
     *           "nombre": "Trnsdsdsdsdsdo 11",
     *           "nombre": "Trnsdsdsdsdsdo 11",
     *           "descripcion": "8:00:00",
     *           "id": 11
     *        }
     *     }
     *
     * @apiError {json} error respuesta con errores.
     * @apiErrorExample {json} Respuesta Errores-Ejemplo
     *     {
     *        "error": {
     *           "nombre": [
     *              "unique"
     *           ]
     *        },
     *        "code": 409
     *     }
     *
     */
	private function ValidarParametros($request){
		$rules = [
			"nombre" => "required|min:3|max:250",
			"paises_id" => "required"
		];
		$v = \Validator::make($request, $rules );

		if ($v->fails()){
			return Response::json($v->errors());
		}
	}
}