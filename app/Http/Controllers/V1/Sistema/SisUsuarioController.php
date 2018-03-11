<?php
namespace App\Http\Controllers\V1\Sistema;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Sistema\SisUsuariosNotificaciones;
use Request;
use Response;
use Illuminate\Support\Facades\Input;
use DB; 
use Hash;
use App\Models\Sistema\SisUsuario;
use App\Models\Sistema\SisUsuariosContactos;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
* Controlador SisUSuario
*
 * @package    UGUS API
 * @subpackage Controlador
 * @author     Luis Alberto Valdez Lescieur <luisvl13@gmail.com>
* @created    2017-08-20
*
* Controlador `SisUSuario`: Manejo de usuarios del sistema
*
*/
class SisUSuarioController extends Controller {
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
		$usuario = SisUsuario::with("SisUsuariosGrupos")->where("email", $obj->get('email'))->first();
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
				$data = SisUSuario::where("activo",1)->with("SisUsuariosGrupos", "SisUsuariosContactos")->orderBy($order, $orden);
				
				$search = trim($valor);
				$keyword = $search;
				$data = $data->whereNested(function($query) use ($keyword){	
						$query->Where("email", "LIKE", "%".$keyword."%")
							->orWhere("nombre", "LIKE", '%'.$keyword.'%')
							->orWhere("username", "LIKE", '%'.$keyword.'%'); 
				});
				if(!$usuario->es_super)
					$data = $data->whereIn("es_super", 0);
				$total = $data->get();
				$data = $data->skip($pagina-1)->take($datos["limite"])->get();
			}
			else{
				$data = SisUSuario::where("activo",1)->with("SisUsuariosGrupos", "SisUsuariosContactos")->skip($pagina-1)->take($datos["limite"])->orderBy($order, $orden);
				if(!$usuario->es_super)
					$data = $data->whereIn("es_super", 0);
				$data = $data->get();
				$total =  SisUSuario::where("activo",1)->with("SisUsuariosGrupos", "SisUsuariosContactos");
				if(!$usuario->es_super)
					$total = $total->whereIn("es_super", 0);
				$total = $total->get();
			}
			
		}
		else{
			$data = SisUSuario::where("activo",1)->with("SisUsuariosGrupos", "SisUsuariosContactos");
			if(!$usuario->es_super)
					$data = $data->whereIn("es_super", 0);
			$data = $data->get();
			$total = $data;
		}

		if(!$data){
			return Response::json(array("status" => 204, "messages" => "No hay resultados"),204);
		} 
		else {			
			return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data, "total" => count($total)), 200);			
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
		$this->ValidarParametros(Input::json()->all());			
		$datos = (object) Input::json()->all();	
		$success = false;

        DB::beginTransaction();
        try{
            $data = new SisUsuario;
            $success = $this->campos($datos, $data);

        } catch (Exception $e) {
            DB::rollback();
            return Response::json(["status" => 500, 'error' => $e->getMessage()], 500);
        } 
        if ($success){
            DB::commit();
            return Response::json(array("status" => 201,"messages" => "Creado", "data" => $data), 201);
        } 
        else{
            DB::rollback();
            return Response::json(array("status" => 409,"messages" => "Conflicto"), 200);
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
		$this->ValidarParametros(Input::json()->all());	

		$datos = (object) Input::json()->all();		
		$success = false;
        
        DB::beginTransaction();
        try{
        	$data = SisUsuario::find($id);

            if(!$data){
                return Response::json(['error' => "No se encuentra el recurso que esta buscando."], HttpResponse::HTTP_NOT_FOUND);
            }
            
            $success = $this->campos($datos, $data);

        } catch (Exception $e) {
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
        $obj =  JWTAuth::parseToken()->getPayload();
        $usuario = SisUsuario::with("SisUsuariosGrupos")->where("email", $obj->get('email'))->first();
		$success = false;
		if(property_exists($datos, "foto")){
			if($datos->foto != '' && !stripos($datos->foto, $data->username))
        		$datos->foto = $this->convertir_imagen($datos->foto, 'usuario', $datos->username);
		}

        $data->nombre 			 = property_exists($datos, "nombre") 			? $datos->nombre 				: $data->nombre;	
        $data->username 		 = property_exists($datos, "username") 			? $datos->username 				: $data->username;	
        $data->email 			 = property_exists($datos, "email") 			? $datos->email 				: $data->email;	
        $data->password 		 = property_exists($datos, "password") 			? Hash::make($datos->password) 	: $data->password;
        if(!$usuario->es_super)
            $data->es_super			 = property_exists($datos, "es_super") 			? $datos->es_super 				: $data->es_super;
        $data->activo 			 = property_exists($datos, "activo") 			? $datos->activo 				: $data->activo;
        $data->activated		 = property_exists($datos, "activated") 		? $datos->activated 			: 0;		
        $data->direccion 		 = property_exists($datos, "direccion") 		? $datos->direccion 			: $data->direccion;
		$data->numero_exterior 	 = property_exists($datos, "numero_exterior") 	? $datos->numero_exterior 		: $data->numero_exterior;
		$data->numero_interior   = property_exists($datos, "numero_interior") 	? $datos->numero_interior 		: $data->numero_interior;
		$data->colonia 			 = property_exists($datos, "colonia") 			? $datos->colonia 				: $data->colonia;
		$data->codigo_postal 	 = property_exists($datos, "codigo_postal") 	? $datos->codigo_postal 		: $data->codigo_postal;
		$data->comentario 	     = property_exists($datos, "comentario") 		? $datos->comentario 			: $data->comentario;
		$data->avatar 			 = property_exists($datos, "avatar") 			? $datos->avatar 				: $data->avatar;
		$data->foto 			 = property_exists($datos, "foto") 				? $datos->foto 					: $data->foto;
		$data->spam 			 = property_exists($datos, "spam") 				? $datos->spam 					: $data->spam;
		$data->localidades_id 	 = property_exists($datos, "localidades_id") 	? $datos->localidades_id 		: $data->localidades_id;
		$data->estados_id 		 = property_exists($datos, "estados_id") 		? $datos->estados_id 			: $data->estados_id;
		$data->municipios_id 	 = property_exists($datos, "municipios_id") 	? $datos->municipios_id 		: $data->municipios_id;
		$data->cargos_id 	     = property_exists($datos, "cargos_id") 	    ? $datos->cargos_id 		    : $data->cargos_id;

        if($datos->permisos != ''){
            if(count($datos->permisos) > 0) {
                $data->permisos = json_encode($datos->permisos);
            }
        }

        if ($data->save()) {
        	if(property_exists($datos, "sis_usuarios_grupos")){
            	DB::table('sis_usuarios_grupos')->where('sis_usuarios_id', $data->id)->delete();
            	foreach($datos->sis_usuarios_grupos as $valor){
            		if(is_array($valor))
            			$valor = (object) $valor;
            		DB::table('sis_usuarios_grupos')->insert(
					    ['sis_usuarios_id' => $data->id, 'sis_grupos_id' => $valor->id]
					);
            	}	
            }
            if(property_exists($datos, "sis_usuarios_clues")){
                DB::table('clue_usuario')->where('sis_usuarios_id', $data->id)->delete();
                foreach($datos->sis_usuarios_clues as $valor){
                    if(is_array($valor))
                    $valor = (object) $valor;
                    DB::table('clue_usuario')->insert(
					    ['sis_usuarios_id' => $data->id, 'clues' => $valor->clues]
					);
            	}
        	}


        	if(property_exists($datos, "sis_usuarios_contactos")){
        		$medios = array_filter($datos->sis_usuarios_contactos, function($v){return $v !== null;});
        		SisUsuariosContactos::where("sis_usuarios_id", $data->id)->delete();
        		foreach ($medios as $key => $value) {
        			$value = (object) $value;
        			if($value != null){
        				DB::update("update sis_usuarios_contactos set deleted_at = null where sis_usuarios_id = $data->id and valor = '$value->valor' ");
        				$item = SisUsuariosContactos::where("sis_usuarios_id", $data->id)->where("valor", $value->valor)->first();

        				if(!$item)
            				$item = new SisUsuariosContactos;

            			$item->sis_usuarios_id = $data->id;
            			$item->tipos_medios_id = $value->tipos_medios_id;
            			$item->valor           = $value->valor;

            			$item->save();
            		}
        		}
        	}
            if(property_exists($datos, "sis_usuarios_notificaciones")){
                $notificaciones = array_filter($datos->sis_usuarios_notificaciones, function($v){return $v !== null;});
                SisUsuariosNotificaciones::where("sis_usuarios_id", $data->id)->delete();
                foreach ($notificaciones as $key => $value) {
                    $value = (object) $value;
                    if($value != null){
                        DB::update("update sis_usuarios_notificaciones set deleted_at = null where sis_usuarios_id = $data->id and tipos_notificaciones_id = '$value->id' ");
                        $item = SisUsuariosNotificaciones::where("sis_usuarios_id", $data->id)->where("tipos_notificaciones_id", $value->id)->first();

                        if(!$item)
                            $item = new SisUsuariosNotificaciones;

                        $item->sis_usuarios_id          = $data->id;
                        $item->tipos_notificaciones_id  = $value->id;

                        $item->save();
                    }
                }
            }
			$success = true;
		}  
		return $success;     						
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
        $data = SisUsuario::with("SisUsuariosGrupos", "cargos", "municipios", "localidades", "SisUsuariosContactos", "SisUsuariosClues", "SisUsuariosNotificaciones")->find($id);

        if(!$data){
            return Response::json(array("status"=> 404,"messages" => "No hay resultados"),404);
        }
        else {
            $permiso=[];
            if(isset($data->SisUsuariosGrupos)){
                foreach($data->SisUsuariosGrupos as $value){
                    if(isset($value->permisos)){
                        foreach(json_decode($value->permisos, true) as $k => $v){
                            if($v==1){
                                if(!array_key_exists($k, $permiso)) {
                                    $permiso[$k] = $v;
                                }
                            }
                        }
                    }
                }
            }
            $data->permisos_grupos = json_encode($permiso);
            return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
        }
    }

    /**
     * Elimine el registro especificado del la base de datos (softdelete).
     *
     * @param  int $id que corresponde al identificador del dato a eliminar
     *
     * @return Response
     * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
     * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
     * @throws \Exception
     */
	public function destroy($id){
		$success = false;
        DB::beginTransaction();
        try {
			$data = SisUSuario::find($id);
			$grupos = $data->SisUsuariosGrupos();
			if(count($grupos)>0){
				foreach ($grupos as $grupo) {
					$data->removeGroup($grupo);				
				}
			}
			$data->delete();
			
			$success=true;
		} 
		catch (Exception $e) {
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
	 * Validad los parametros recibidos, Esto no tiene ruta de acceso es un metodo privado del controlador.
	 *
	 * @param  Request  $request que corresponde a los parametros enviados por el cliente
	 *
	 * @return Response
	 * <code> Respuesta Error json con los errores encontrados </code>
	 */
	private function ValidarParametros($request){
		$rules = [
			"email" => "required|min:3|email",
			"sis_usuarios_grupos" => "required|array"
		];
		$v = \Validator::make(Request::json()->all(), $rules );

		if ($v->fails()){
			return Response::json($v->errors());
		}
	}
}