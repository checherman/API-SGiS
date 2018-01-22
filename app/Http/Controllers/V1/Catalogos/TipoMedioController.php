<?php
namespace App\Http\Controllers\v1\Catalogos;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Input;
use DB; 
use App\Models\Catalogos\TiposMedios;
/**
 * Controlador TipoMedio
 *
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
 * @created    2017-03-22
 *
 * Controlador `TipoMedio`: Controlador  para el manejo de catalogo de tipos de medios
 *
 */
class TipoMedioController extends Controller {

    /**
     * @api {get} /tipos-medios 1.Listar tipos medios
     * @apiVersion 1.0.0
     * @apiName GetTipoMedio
     * @apiGroup Catalogo/TipoMedioController
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
				$data = TiposMedios::orderBy($order,$orden);
				
				$search = trim($valor);
				$keyword = $search;
				$data = $data->whereNested(function($query) use ($keyword){					
					$query->Where('nombre', 'LIKE', '%'.$keyword.'%'); 
				});
				
				$total = $data->get();
				$data = $data->skip($pagina-1)->take($datos['limite'])->get();
			}
			else{
				$data = TiposMedios::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
				$total = TiposMedios::all();
			}
			
		}
		else{
			$data = TiposMedios::all();
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
     * @api {post} /tipos-medios 2.Crea nuevo Medio
     * @apiVersion 1.0.0
     * @apiName PostTipoMedio
     * @apiGroup Catalogo/TipoMedioController
     * @apiPermission Admin
     *
     * @apiDescription Crea un nuevo tipo Medio.
     *
     * @apiParam {String} nombre Nombre del Tipo Medio.
     * @apiParam {String} descripcion Descripcion del Tipo Medio.
     *
     * @apiSuccess {String} id         informacion del nuevo Tipo Medio.
     *
     */
	public function store(){
		$this->ValidarParametros(Input::json()->all());			
		$datos = (object) Input::json()->all();	
		$success = false;

        DB::beginTransaction();
        try{
            $data = new TiposMedios;
            $success = $this->campos($datos, $data);

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(["status" => 500, 'error' => $e->getMessage()], 500);
        } 
        if ($success){
            DB::commit();
            return Response::json(array("status" => 201,"messages" => "Creado","data" => $data), 201);
        } 
        else{
            DB::rollback();
            return Response::json(array("status" => 409,"messages" => "Conflicto"), 200);
        }
		
	}

    /**
     * @api {get} /tipos-medios/:id 3.Consulta datos de un Tipo Medio
     * @apiVersion 1.0.0
     * @apiName ShowTipoMedio
     * @apiGroup Catalogo/TipoMedioController
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
	public function update($id){
		$this->ValidarParametros(Input::json()->all());	

		$datos = (object) Input::json()->all();		
		$success = false;
        
        DB::beginTransaction();
        try{
        	$data = TiposMedios::find($id);

            if(!$data){
                return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
            }
            
            $success = $this->campos($datos, $data);

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(["status" => 500, 'error' => $e->getMessage()], 500);
        } 
        if($success){
			DB::commit();
			return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
		} 
		else {
			DB::rollback();
			return Response::json(array("status" => 304, "messages" => "No modificado"),200);
		}
	}

	public function campos($datos, $data){
		$success = false;

        $data->nombre 		= property_exists($datos, "nombre") ? $datos->nombre: $data->nombre;	
        if ($data->save()) {        	
			$success = true;
		}  
		return $success;     						
	}
	
    /**
     * @api {put} /tipos-medios/:id 4.Actualiza Tipo Medio
     * @apiVersion 1.0.0
     * @apiName PutTipoMedio
     * @apiGroup Catalogo/TipoMedioController
     * @apiPermission Admin
     *
     * @apiDescription Actualiza un Tipo Medio.
     *
     * @apiParam {number} id del Tipo Medio que se quiere editar.
     * @apiParam {String} nombre Nombre del Tipo Medio.
     * @apiParam {String} descripcion Descripcion del Tipo Medio.
     **
     */
	public function show($id){
		$data = TiposMedios::find($id);			
		
		if(!$data){
			return Response::json(array("status"=> 404,"messages" => "No hay resultados"),404);
		} 
		else {				
			return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
		}
	}

    /**
     * @api {destroy} /tipos-medios/:id 5.Elimina Tipo Medio
     * @apiVersion 1.0.0
     * @apiName DestroyTipoMedio
     * @apiGroup Catalogo/TipoMedioController
     * @apiPermission Admin
     *
     * @apiDescription Elimina un Tipo Medio.
     *
     * @apiParam {number} id del Tipo Medio que se quiere editar.
     **
     */
	public function destroy($id){
		$success = false;
        DB::beginTransaction();
        try {
			$data = TiposMedios::find($id);			
			$data->delete();
			
			$success=true;
		} 
		catch (\Exception $e) {
			return Response::json($e->getMessage(), 500);
        }
        if ($success){
			DB::commit();
			return Response::json(array("status" => 200,"messages" => "Operación realizada con exito", "data" => $data), 200);
		} 
		else {
			DB::rollback();
			return Response::json(array("status" => 404, "messages" => "No se encontro el registro"), 404);
		}
	}

    /**
     * @api /estados 6.ValidarParametros
     * @apiVersion 1.0.0
     * @apiName TipoMedioValidarParametros
     * @apiGroup Catalogo/TipoMedioController
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
			"nombre" => "required|min:3"
		];
		$v = \Validator::make(Request::json()->all(), $rules );

		if ($v->fails()){
			return Response::json($v->errors());
		}
	}
}