<?php
namespace App\Http\Controllers\v1\Sistema;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Input;
use DB; 
use Hash;
use JWTAuth;
use App\Models\Sistema\SisUSuario;
use App\Models\Sistema\Notificaciones;
use App\Models\Sistema\NotificacionesUsuarios;

/**
* Controlador SisUSuario
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Controlador `SisSisUSuario`: Manejo de usuarios del sistema
*
*/
class NotificacionController extends Controller {
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
		$obj =  JWTAuth::parseToken()->getPayload();
		$usuario = SisUsuario::where("email", $obj->get('email'))->first();
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
			if(array_key_exists("buscar", $datos)){
				$columna = $datos["columna"];
				$valor   = $datos["valor"];
				$data = NotificacionesUsuarios::with("Notificaciones")->where("usuarios_id",$usuario->id)->orderBy($order, $orden);
				
				$search = trim($valor);
				$keyword = $search;
				$data = $data->whereNested(function($query) use ($keyword){	
						$query->Where("mensaje", "LIKE", "%".$keyword."%")
							->orWhere("tipo", "LIKE", '%'.$keyword.'%'); 
				});				
				$total = $data->get();
				$data = $data->skip($pagina-1)->take($datos["limite"])->get();
			}
			else{
				$data = NotificacionesUsuarios::with("Notificaciones")->where("usuarios_id",$usuario->id)
				->skip($pagina-1)->take($datos["limite"])->orderBy($order, $orden);
				
				$data = $data->get();
				$total =  NotificacionesUsuarios::with("Notificaciones")->where("usuarios_id",$usuario->id);
				
				$total = $total->get();
			}
			
		}
		else{
			$data = NotificacionesUsuarios::with("Notificaciones")->where("usuarios_id",$usuario->id);			
			$data = $data->get();
			$total = $data;
		}

		if(!$data){
			return Response::json(array("status" => 204, "messages" => "No hay resultados"),204);
		} 
		else {	
			$total_n = NotificacionesUsuarios::with("Notificaciones")->where("usuarios_id",$usuario->id)->where("leido", null)->get();		
			$notificaciones = [];
			foreach ($data as $key => $value) {
				$mensaje = json_decode($value->notificaciones->mensaje);
				$mensaje->mensaje->leido = $value->leido;
				$mensaje->mensaje->tipo = $value->tipo;
				array_push($notificaciones, $mensaje);				
			}
			return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $notificaciones, "total" => count($total), "total_n" => count($total_n)),  200);			
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
		$datos = (object) Input::json()->all();		
		$success = false;
        
        DB::beginTransaction();
        try{
        	$obj =  JWTAuth::parseToken()->getPayload();
			$usuario = SisUsuario::where("email", $obj->get('email'))->first();

        	$data = Notificaciones::find($id);

            if(!$data){
                return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
            } 
            else{
            	$notificacion = NotificacionesUsuarios::where("notificaciones_id", $data->id)->where("usuarios_id", $usuario->id)->first();

            	if(property_exists($datos, "leido"))
        			$notificacion->leido = date("Y-m-d h:i:s");

        		if(property_exists($datos, "enviado"))
        			$notificacion->enviado = date("Y-m-d h:i:s");

        		if($notificacion->save())
        			$success = true;
            }                     

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
}