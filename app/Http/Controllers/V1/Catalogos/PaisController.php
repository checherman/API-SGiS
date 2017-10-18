<?php
namespace App\Http\Controllers\v1\Catalogos;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Input;
use DB; 
use App\Models\Catalogos\Paises;
/**
* Controlador Acción
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Controlador `Paises`: Esto es un ejemplo
*
*/
class PaisController extends Controller {
	 
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
	 * Paises ordenamiento con respecto a id:
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
				$data = Paises::orderBy($order,$orden);
				
				$search = trim($valor);
				$keyword = $search;
				$data = $data->whereNested(function($query) use ($keyword){					
					$query->Where('nombre', 'LIKE', '%'.$keyword.'%'); 
				});
				
				$total = $data->get();
				$data = $data->skip($pagina-1)->take($datos['limite'])->get();
			}
			else{
				$data = Paises::skip($pagina-1)->take($datos['limite'])->orderBy($order, $orden)->get();
				$total = Paises::all();
			}
			
		}
		else{
			$data = Paises::with("estados")->get();
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
	 * Crear un nuevo registro en la base de datos con los datos enviados
	 *
	 * <h4>Request</h4>
	 * Recibe un input request tipo json de los datos a almacenar en la tabla correspondiente
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 201, "messages": "Creado", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
	 */
	public function store(){
		$this->ValidarParametros(Request::json()->all());	
		$datos = Input::json();
		$success = false;
		
        DB::beginTransaction();
        try{
        		
            $data = new Paises;
            $data->nombre = $datos->get('nombre');
            $data->clave_ISOA2 = $datos->get('clave_ISOA2');
            $data->clave_A3 = $datos->get('clave_A3');
            $data->clave_N3 = $datos->get('clave_N3');
            $data->prefijo_telefono = $datos->get('prefijo_telefono');

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
	 * Devuelve la información del registro especificado.
	 *
	 * @param  int  $id que corresponde al identificador del recurso a mostrar
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
	 */
	public function show($id){
		$data = Paises::find($id);

		if(!$data){
			return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
		} 
		else{
			return Response::json(array("status" => 200,"messages" => "Operación realizada con exito","data" => $data), 200);
		}
	}


	/**
	 * Actualizar el  registro especificado en el la base de datos
	 *
	 * <h4>Request</h4>
	 * Recibe un Input Request con el json de los datos
	 *
	 * @param  int  $id que corresponde al identificador del dato a actualizar 	 
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 304, "messages": "No modificado"),status) </code>
	 */
	public function update($id){
		$this->ValidarParametros(Request::json()->all());	
	
		$datos = Request::json(); 
		$success = false;
        DB::beginTransaction();
        try{
			$data = Paises::find($id);
				
            $data->nombre = $datos->get('nombre');
            $data->clave_ISOA2 = $datos->get('clave_ISOA2');
            $data->clave_A3 = $datos->get('clave_A3');
            $data->clave_N3 = $datos->get('clave_N3');
            $data->prefijo_telefono = $datos->get('prefijo_telefono');
            
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
	 * Elimine el registro especificado del la base de datos (softdelete).
	 *
	 * @param  int  $id que corresponde al identificador del dato a eliminar
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
	 */
	public function destroy($id)
	{
		$success = false;
        DB::beginTransaction();
        try {
			$data = Paises::find($id);
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
	 * Validad los parametros recibidos, Esto no tiene ruta de acceso es un metodo privado del controlador.
	 *
	 * @param  Request  $request que corresponde a los parametros enviados por el cliente
	 *
	 * @return Response
	 * <code> Respuesta Error json con los errores encontrados </code>
	 */
	private function ValidarParametros($request){
		$rules = [
			"nombre" => "required|min:3|max:250"
		];
		$v = \Validator::make($request, $rules );

		if ($v->fails()){
			return Response::json($v->errors());
		}
	}
}