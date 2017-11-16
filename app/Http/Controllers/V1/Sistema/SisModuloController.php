<?php
namespace App\Http\Controllers\v1\Sistema;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Illuminate\Support\Facades\Input;
use DB; 
use Session;
use App\Models\Sistema\SisModulo;
use App\Models\Sistema\SisModuloAccion;
/**
* Controlador Modulo
*
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
* @created    2017-08-20
*
* Controlador `SisModulo`: Manejo los permisos(modulo)
*
*/
class SisModuloController extends Controller {

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
	 * Ejemplo ordenamiento con respecto a id:
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
		if(array_key_exists("pagina", $datos)){
			$pagina = $datos["pagina"];
			if(isset($datos["order"])){
				$order = $datos["order"];
				if(strpos(" ".$order,"-"))
					$orden = "desc";
				else
					$orden = "asc";
				$order=str_replace("-", "", $order); 
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
			if(array_key_exists("buscar",  $datos)){
				$columna = $datos["columna"];
				$valor   = $datos["valor"];
				$data = SisModulo::with("Padres")->orderBy($order, $orden);
				
				$search = trim($valor);
				$keyword = $search;
				$data = $data->whereNested(function($query) use ($keyword){					
						$query->Where("nombre", "LIKE", "%".$keyword."%")
							 ->orWhere("controlador", "LIKE", "%".$keyword."%")
							 ->orWhere("vista", "LIKE", "%".$keyword."%")
							 ->orWhere("sis_modulos_id", "LIKE", "%".$keyword."%"); 
				});
				$total = $data->get();
				$data = $data->skip($pagina-1)->take($datos["limite"])->get();
			}
			else{
				$data = SisModulo::with("Padres")->skip($pagina-1)->take($datos["limite"])->orderBy($order, $orden)->orderBy("sis_modulos_id", "ASC")->get();
				$total = SisModulo::with("Padres")->get();
			}
			
		}
		else{
			$data = SisModulo::with("Padres")->orderBy("sis_modulos_id", "ASC")->get();
			$total = $data;
		}

		if(!$data){
			return Response::json(array("status" => 404,"messages" => "No hay resultados"), 404);
		} 
		else{

			foreach ($data as $key => $value) {
				$value["modulo_padre"] = $value["padres"]["nombre"];
			}
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
        try {
            $data = new SisModulo;
            $data->nombre = $datos->get("nombre");
			$data->sis_modulos_id = $datos->get("sis_modulos_id") == '' ? null : $datos->get("sis_modulos_id");
			$data->controlador = $datos->get("controlador");
			$data->vista = $datos->get("vista")?"1":"0";
			$data->es_super = $datos->get("es_super")?"1":"0";

            if ($data->save()) {
				// acciones (funciones) a los que se puede acceder en el controller
				foreach($datos->get("metodos") as $item){
					$dataAccion = new SisModuloAccion;
					$dataAccion->nombre = $item["nombre"];				
					$dataAccion->metodo = $item["metodo"];
					$dataAccion->recurso = $item["recurso"];
					$dataAccion->es_super = $item["es_super"];
					$dataAccion->sis_modulos_id = $data->id;
					$dataAccion->save();						
				}
				$success = true;
			}
        } 
		catch(\Exception $e){
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
		$data = SisModulo::with("Padres")->find($id);

		if(!$data){
			return Response::json(array("status"=> 204, "messages" => "No hay resultados"), 204);
		} 
		else {
			$data["metodos"] = SisModuloAccion::where("sis_modulos_id",$id)->get()->toArray();
			return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
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
		$datos = Input::json(); 
		$success = false;

        DB::beginTransaction();
        try {
			$data = SisModulo::find($id);
			$data->nombre = $datos->get("nombre");
			$data->sis_modulos_id = $datos->get("sis_modulos_id") == '' ? null : $datos->get("sis_modulos_id");
			$data->controlador = $datos->get("controlador");
			$data->vista = $datos->get("vista");
			$data->es_super = $datos->get("es_super");

            if ($data->save()) {
				foreach($datos->get("metodos") as $item){					
					$dataAccion = SisModuloAccion::where("sis_modulos_id",$id)->where("nombre",$item["nombre"])->where("metodo",$item["metodo"])->first();
				
					if(!$dataAccion)
						$dataAccion = new SisModuloAccion;					
					
					$dataAccion->nombre = $item["nombre"];				
					$dataAccion->metodo = $item["metodo"];
					$dataAccion->recurso = $item["recurso"];
					$dataAccion->sis_modulos_id = $id;
					$dataAccion->es_super = $item["es_super"];
					$dataAccion->save();						
				}
				$i=array();
				// Validar las acciones a quitar que no existan en los datos enviados por el usuario
				$dataAccion = SisModuloAccion::where("sis_modulos_id",$id)->get();
				if(count($dataAccion)>count($datos->get("metodos"))){
					foreach($dataAccion as $ma){
						foreach($datos->get("metodos") as $item){
							if($ma->sis_modulos_id == $id && $ma->nombre ==  $item["nombre"] && $ma->metodo == $item["metodo"]){
								array_push($i,$ma->id);
							}							
						}
					}
					$dataAccion = SisModuloAccion::where("sis_modulos_id",$id)->whereNotIn("id",$i)->delete();
				}
				$success = true;
			}
		} 
		catch (Exception $e) {
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
	public function destroy($id){
		$success = false;
        DB::beginTransaction();
        try {
			$data = SisModulo::find($id);
			$data->delete();
			$success=true;
		} 
		catch (Exception $e) {
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
	 * Muestra una lista de las acciones que corresponde a cada modulo (controller).
	 * 
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 404, "messages": "No hay resultados"),status) </code>
	 */
	public function permiso(){
		try {			
			$padres = SisModulo::where("sis_modulos_id", null)->orderBy("nombre", "ASC")->get();
			foreach ($padres as $key => $value) {
				$accion = SisModuloAccion::where("sis_modulos_id",$value->id)->orderBy("nombre", "ASC")->get();
				if($accion){
					$value->accion = $accion;
				}
				$hijos = SisModulo::where("sis_modulos_id", $value->id)->orderBy("nombre", "ASC")->get();
				if($hijos){
					$value->hijos = $hijos;
					foreach ($hijos as $k1 => $v1) {
						$accion = SisModuloAccion::where("sis_modulos_id",$v1->id)->orderBy("nombre", "ASC")->get();
						if($accion){
							$v1->accion = $accion;
						}
						$hijos = SisModulo::where("sis_modulos_id", $v1->id)->orderBy("nombre", "ASC")->get();
						if($hijos){
							$v1->hijos = $hijos;
							foreach ($hijos as $k2 => $v2) {
								$accion = SisModuloAccion::where("sis_modulos_id",$v2->id)->orderBy("nombre", "ASC")->get();
								if($accion){
									$v2->accion = $accion;
								}
							}
						}
					}
				}
			}
						

			if(!$padres){
				return Response::json(array("status"=> 404,"messages"=>"No hay resultados"),404);
			} 
			else {							
				return Response::json(array("status"=>200,"messages"=>"Operación realizada con exito","data"=> $padres ),200);
			}
		} 
		catch (Exception $e) {
			return Response::json($e->getMessage(), 500);
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
			"nombre" => "required|min:3|max:250",
			"metodos"=> "array"
		];
		$v = \Validator::make(Request::json()->all(), $rules );

		if ($v->fails()){
			return Response::json($v->errors());
		}
	}
}
