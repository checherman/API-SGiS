<?php
namespace App\Http\Controllers\v1\Sistema;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Exception;
use Illuminate\Support\Facades\Input;
use JWTAuth, JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;

use Request;
use Response;
use Session;
use Crypt;
use Mail;
use DB;
use Hash, Config;
use App\Models\Sistema\SisUsuario;
use App\Models\Sistema\SisUsuariosRfcs;
use App\Models\Sistema\SisUsuariosContactos;

use App\Models\Sistema\Empresas;
use App\Models\Sistema\Sucursales;
use App\Models\Sistema\EmpresaConfiguracion;
use App\Models\Sistema\SucursalConfiguracion;

/**
* Controlador Oauth
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Controlador `Oauth`: Manejo de la obtension del token de acceso
*
*/
class SisOauthController extends Controller {

	/**
	 * Renueva el token de acceso si ya caduco
	 *
	 * <h4>Input</h4>
	 * Recibe un input request con el refresh_token
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("access_token": "token", "access_token": "token"),status) </code>
	 * <code> Respuesta Error json(array(error), status) </code>
	*/
	public function refreshToken(Request $request){
        try{
            $token =  JWTAuth::parseToken()->refresh();
            return response()->json(['access_token' => $token], 200);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expirado'], 401);  
        } catch (JWTException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
	/**
	 * Crear un nuevo token de acceso con las credenciales del usuario
	 *
	 * <h4>Request</h4>
	 * Recibe un input request tipo json de los datos de acceso OAUTH del usuario
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("access_token": "token", "access_token": "token"),status) </code>
	 * <code> Respuesta Error json(array(error), status) </code>
	*/
	public function accessToken(){
		try{
			$disponible_oaut = Request::header('Disponible') == 'false' ? false : true;
	        $credentials = Input::only("email", "password");

	        $clues = Request::header('clues');
			// Si no se puede recibir como POST recibir entonces como json
			if($credentials["email"] == ""){
				$credentials = Input::json()->all();			
			}
			if($disponible_oaut){
		        $post_request =  "grant_type=password"
		                    	."&client_id=".env("CLIENT_ID")
		                    	."&client_secret=".env("CLIENT_SECRET")
		                    	."&username=".$credentials["email"]
		                    	."&password=".$credentials["password"]; 
		                         
		        $ch = curl_init();
		        $header[] = "Content-Type: application/x-www-form-urlencoded";
		        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
		        curl_setopt($ch, CURLOPT_URL, env("OAUTH_SERVER")."/oauth/access_token");
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_request);
		      
		        // Execute & get variables
		        $html = curl_exec($ch);
		        $api_response = json_decode($html); 
		        $curlError = curl_error($ch);
		        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		        curl_close($ch);
		        if($curlError){
		        	 return Response::json("Hubo un problema al intentar hacer la autenticacion. cURL problem: $curlError");
		        }
		        
		        if($http_code != 200){
		          if(isset($api_response->error)){
						return Response::json(["error1" => $api_response->error], $http_code);	
					}else{
						return Response::json(["error2" => $api_response], $http_code);
					}
		        } 
			}

			try{				

				$data = SisUsuario::with("SisUsuariosGrupos")->where("email", $credentials["email"])->orWhere("username", $credentials["email"])->first();					
				if(!$data)
					return Response::json(["error3" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => "NO ENCONTRADO"], 403);		
				if(!$disponible_oaut){					
					if(!Hash::check($credentials['password'], $data->password)){
						return Response::json(["error3" => "invalid_credentials"], 401);		
					}
				}
				Session::put('usuario', $data);
			}
			catch(Exception $e){
				return Response::json(["error4" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => $e->getMessage()], 403);
			}
	        //Encriptamos el refresh token para que no quede 100% expuesto en la aplicacion web
			if($disponible_oaut)
	        	$refresh_token_encrypted = Crypt::encrypt($api_response->refresh_token);
			
	       	//validar cuenta y grupos
	        $permiso=[]; 
	       	if(isset($data->SisUsuariosGrupos)){
				foreach($data->SisUsuariosGrupos as $value){
					if(isset($value->permisos))
					foreach(json_decode($value->permisos, true) as $k => $v){
						if($v==1){
							if(!in_array($k, $permiso))
								array_push($permiso, $k);
						}
					}
				}
			}
			
	        try{
	        	$claims = [
                    "sub" => 1,
                    "email" => $data->email,                    
                ];
                if($disponible_oaut){
                	$claims["access_token"] = $api_response->access_token;
                    $claims["refresh_token"] = $refresh_token_encrypted;
                }
                $payload = JWTFactory::make($claims);
                $token = JWTAuth::encode($payload);
	        }catch(JWTException $e){
	        	return Response::json(["error5" => $e->getMessage()], 500);
	        }
	        $usuario_logueado = $data->id;
	        if($disponible_oaut)
				$usuario = $this->getPerfil(true, $api_response->access_token, $data->email);									        
			else
				$usuario = $this->getPerfil(false);

            $variable = DB::table('clue_usuario')
                ->leftJoin("clues","clues.clues","clue_usuario.clues")
                ->select("clues.clues","clues.nombre")
                ->where('sis_usuarios_id', $usuario_logueado)->get();

            $server_info = [
                "server_datetime_snap" => getdate(),
                "token_refresh_ttl" => Config::get("jwt.refresh_ttl")
            ];

	        return Response::json(["usuario" => $data, "access_token" => $token->get(), 'server_info'=> $server_info, "permisos" => $permiso, "usuario_clues" => $variable], 200);
	    }catch(Exception $e){
	         return Response::json(["error6" => $e->getMessage()], 500);
	    }		
	}


	/**
	 * Valida la cuenta del usuario que tenga acceso a al sistema no unicamente en OAUTH
	 *
	 * <h4>Request</h4>
	 * Recibe un Input Request con el json de los datos
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("data": "Vinculación exitosa"),status) </code>
	 * <code> Respuesta Error json(array(error), status) </code>
	*/
	public function validarCuenta(){
		try{
			$obj =  JWTAuth::parseToken()->getPayload();
			$permiso=[]; $configuracion = [];
			$data = SisUsuario::with("SisUsuariosGrupos")->where("email", $obj->get('email'))->first();

			if(!$data){
				return Response::json(["error" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => "NO ENCONTRADO"], 403);
			}
			else{
				
				if(isset($data->SisUsuariosGrupos)){
					foreach($data->SisUsuariosGrupos as $value){
						if(isset($value->permisos))
						foreach(json_decode($value->permisos, true) as $k => $v){
							if($v==1){
								if(!in_array($k, $permiso))
									array_push($permiso, $k);
							}
						}
					}
				}
				
				$variable = SucursalConfiguracion::all();

				foreach ($variable as $key => $value) {
					$configuracion[$value->clave] = json_decode($value->valor);
				}
														
			}			
			Session::put('usuario', $data);
		}
		catch (Exception $e){
			return Response::json(["error" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => $e->getMessage()], 403);
		}
        
        try{
        
	        $access_token = str_replace("Bearer ","", $obj->get('access_token'));	
	        $post_request = "access_token=".$access_token; 
	                 	                   
	        $ch = curl_init();
	        $header[]         = "Content-Type: application/x-www-form-urlencoded";
	        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
	        curl_setopt($ch, CURLOPT_URL, env("OAUTH_SERVER")."/oauth/vinculacion");
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_request);
	         
	        // Execute & get variables
	        $api_response = json_decode(curl_exec($ch)); 
	        $curlError = curl_error($ch);
	        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        curl_close($ch);
	        if($curlError){ 
	        	 return Response::json("Hubo un problema al intentar hacer la vinculación. cURL problem: $curlError");
	        }
	        
	        if($http_code != 200){
	            return Response::json(["error" => $api_response->error], $http_code);
	        }        
	        	        	                  
	        return Response::json(["data" => "Vinculación exitosa", "permisos" => $permiso, "configuracion" => $configuracion], 200);
	    }catch(Exception $e){
	         return Response::json(["error" => $e->getMessage()], 500);
	    }
	}

	/**
	 * Devuelve la información de los permisos del usuario, Este metodo deve ser llamado depues del access_token 	 
	 *
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array(permisos), status) </code>
	*/
	public function permisosAutorizados(){
				
		try{
			$obj =  JWTAuth::parseToken()->getPayload();
			$data = SisUsuario::with("SisUsuariosGrupos")->where("email", $obj->get('email'))->first(); 
			              
			if(!$data){
				return Response::json(["error" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => "NO ENCONTRADO"], 403);
			}
			else{
				$permiso=[];

				if(isset($data->SisUsuariosGrupos)){
					foreach($data->SisUsuariosGrupos as $value){
						if(isset($value->permisos))
						foreach(json_decode($value->permisos, true) as $k => $v){
							if($v==1){
								if(!in_array($k, $permiso))
									array_push($permiso, $k);
							}
						}
					}
				}

				$data = []; 
				$total = $data;

				Session::put('usuario', $data); 										
				return Response::json(["permisos" => $permiso, "data" => $data, "total" => $total], 200);
			}																						
		}catch(Exception $e){
			return Response::json(["error" => $e->getMessage()], 500);
		}
	}

	/**
	 * Actualiza el perfil del usuario.
	 *
	 * <h4>Request</h4>
	 * Recibe un Input Request con el json de los datos
	 * @param  string  $email que corresponde al identificador del usuario a actualizar. 
	 *	 
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
	 */
	public function perfil($id)
	{
		$data = $this->getPerfil(true);

		if(!$data){
			return Response::json(array("status"=> 404,"messages" => "No hay resultados"),404);
		} 
		else {				
			return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
		}
	}
	
	public function getPerfil($valor, $token = null, $email  = null){
		$disponible_oaut = Request::header('Disponible') == 'false' ? false : true;
		try{			
			$obj =  JWTAuth::parseToken()->getPayload();
			$email = $obj->get('email');
			if(!$valor){				
				$token = $obj->get('access_token');				
			}
			if($disponible_oaut){
				$token = $obj->get('access_token');
				if($email){
					$access_token = 'Bearer '.$token;	
				
					$ch = curl_init();
					$headers = array(
						"Content-Type: application/x-www-form-urlencoded",       					
						"X-Usuario: ".$email,
						"Authorization: ".$access_token
					);
					curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
					curl_setopt($ch, CURLOPT_URL, env('OAUTH_SERVER').'/api/v1/perfil');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					
					// Execute & get variables
					$api_response = json_decode(curl_exec($ch));
					$curlError = curl_error($ch);
					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					
					if($curlError){ 
						 return Response::json("Hubo un problema al validar el token de acceso. cURL problem: $curlError");
					 
					// Tet if there is a 4XX error (request went through but erred). 
					}
					
					if($http_code != 200){
						if(isset($api_response->error)){
							if(!$valor)
								return Response::json(['error'=>$api_response->error],$http_code);	
							else return false;
						}else{
							if(!$valor)
								return Response::json(['error'=>$api_response],$http_code);
							else return false;
						}
					}
					if($valor){					
						return $api_response;
					}
					else{
						return Response::json(array("status" =>200,"messages" => "Ok", "data" => $api_response),200);			
					}
			    }
				else{
					return Response::json(array("status" =>404,"messages" => "No encontrado"),404);	
				}
			}
			else{
				$data = SisUsuario::with("SisUsuariosGrupos","SisUsuariosRfcs", "SisUsuariosContactos")->where("email", $email)->first();					
				if($valor){					
					return $data;
				}
				else{
					return Response::json(array("status" =>200,"messages" => "Ok", "data" => $data),200);			
				}

			}
		}catch(Exception $e){
			return Response::json(["error" => $e->getMessage()], 500);
		}
	}

	/**
	 * Actualiza el perfil del usuario.
	 *
	 * <h4>Request</h4>
	 * Recibe un Input Request con el json de los datos
	 * @param  string  $email que corresponde al identificador del usuario a actualizar. 
	 *	 
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
	 */
	public function actualizarFoto($email){
		try{
			$obj =  JWTAuth::parseToken()->getPayload();
			if($email == $obj->get('email')){
				$datos = json_encode(Request::json()->all());
				$access_token = 'Bearer '.$obj->get('access_token');		
				
				$ch = curl_init();
				$headers = array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($datos),     					
					'X-Usuario: '.$email,
					'Authorization: '.$access_token
				);
				curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
				curl_setopt($ch, CURLOPT_URL, env('OAUTH_SERVER').'/api/v1/actualizar-foto/'.$email);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
				
				// Execute & get variables
				$api_response = json_decode(curl_exec($ch)); 
				$curlError = curl_error($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				
				if($curlError){ 
					 return Response::json("Hubo un problema al validar el token de acceso. cURL problem: $curlError");
				 
				// Tet if there is a 4XX error (request went through but erred). 
				}
				
				if($http_code != 200){
					if(isset($api_response->error)){
						return Response::json(['error'=>$api_response->error],$http_code);	
					}else{
						return Response::json(['error'=>$api_response],$http_code);
					}
				}   
				return Response::json(array("status" =>200,"messages" => "Ok", "data" => $api_response),200);
			}											
			else{
				return Response::json(array("status" =>404,"messages" => "No encontrado"),404);	
			}
		}catch(Exception $e){
			return Response::json(["error" => $e->getMessage()], 500);
		}
	}
	/**
	 * Actualiza el perfil del usuario.
	 *
	 * <h4>Request</h4>
	 * Recibe un Input Request con el json de los datos
	 * @param  string  $email que corresponde al identificador del usuario a actualizar. 
	 *	 
	 * @return Response
	 * <code style="color:green"> Respuesta Ok json(array("status": 200, "messages": "Operación realizada con exito", "data": array(resultado)),status) </code>
	 * <code> Respuesta Error json(array("status": 500, "messages": "Error interno del servidor"),status) </code>
	 */
	public function actualizarPerfil($id)
	{
		$disponible_oaut = Request::header('Disponible') == 'false' ? false : true;	

		try{			
			$obj =  JWTAuth::parseToken()->getPayload();
			if($disponible_oaut){
				if($id == $obj->get('email')){
					$datos = json_encode(Request::json()->all());
					$access_token = 'Bearer '.$obj->get('access_token');		
					if($disponible_oaut){
						$ch = curl_init();
						$headers = array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($datos),     					
							'X-Usuario: '.$email,
							'Authorization: '.$access_token
						);		
						curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
						curl_setopt($ch, CURLOPT_URL, env('OAUTH_SERVER').'/api/v1/actualizar-perfil/'.$email);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
						curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
						
						// Execute & get variables
						$api_response = json_decode(curl_exec($ch)); 
						$curlError = curl_error($ch);
						$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						
						if($curlError){ 
							 return Response::json("Hubo un problema al validar el token de acceso. cURL problem: $curlError");
						 
						// Tet if there is a 4XX error (request went through but erred). 
						}
					}
					if($http_code != 200){
						if(isset($api_response->error)){
							return Response::json(['error'=>$api_response->error],$http_code);	
						}else{
							return Response::json(['error'=>$api_response],$http_code);
						}
					}   
					return Response::json(array("status" =>200,"messages" => "Ok", "data" => $api_response),200);
				}else{
					return Response::json(array("status" =>404,"messages" => "No encontrado"),404);	
				}
			}
			else{
				$datos = Input::json()->all();
				if(is_array($datos))
					$datos = (object) $datos;
				$data = SisUsuario::where("email", $obj->get('email'))->first();

				$success = $this->campos($datos, $data);

				if($success){
					return Response::json(array("status" => 200, "messages" => "Operación realizada con exito", "data" => $data), 200);
				} 
				else {
					return Response::json(array("status" => 304, "messages" => "No modificado"),200);
				}
			}
		}catch(Exception $e){
			return Response::json(["error" => $e->getMessage()], 500);
		}
	}

	public function campos($datos, $data){
		$success = false;
		if(property_exists($datos, "foto")){
			if($datos->foto != '' && !stripos($datos->foto, $data->username))
        		$datos->foto = $this->convertir_imagen($datos->foto, 'usuario', $datos->username);
		}

        $data->nombre 			 = property_exists($datos, "nombre") 			? $datos->nombre : '';	
        $data->username 		 = property_exists($datos, "username") 			? $datos->username : '';	
        $data->email 			 = property_exists($datos, "email") 			? $datos->email : '';	
        $data->password 		 = property_exists($datos, "password") 			? Hash::make($datos->password) : $data->password;
        $data->activo 			 = property_exists($datos, "activo") 			? $datos->activo : '0';
        $data->activated		 = property_exists($datos, "activated") 		? $datos->activated : '0';		
        $data->direccion 		 = property_exists($datos, "direccion") 		? $datos->direccion : '';
		$data->numero_exterior 	 = property_exists($datos, "numero_exterior") 	? $datos->numero_exterior : '';
		$data->numero_interior   = property_exists($datos, "numero_interior") 	? $datos->numero_interior : '';
		$data->colonia 			 = property_exists($datos, "colonia") 			? $datos->colonia : '';
		$data->codigo_postal 	 = property_exists($datos, "codigo_postal") 	? $datos->codigo_postal : '';
		$data->comentario 	     = property_exists($datos, "comentario") 		? $datos->comentario : '';
		$data->avatar 			 = property_exists($datos, "avatar") 			? $datos->avatar : '';
		$data->foto 			 = property_exists($datos, "foto") 				? $datos->foto : '';
		$data->spam 			 = property_exists($datos, "spam") 				? $datos->spam : '';
		$data->paises_id 		 = property_exists($datos, "paises_id") 		? $datos->paises_id : '';
		$data->estados_id 		 = property_exists($datos, "estados_id") 		? $datos->estados_id : '';
		$data->municipios_id 	 = property_exists($datos, "municipios_id") 	? $datos->municipios_id : '';	

        if ($data->save()) {
        	if(property_exists($datos, "grupo")){
            	DB::table('sis_usuarios_grupos')->where('sis_usuarios_id', $data->id)->delete();
            	foreach($datos->grupo as $valor){
            		DB::table('sis_usuarios_grupos')->where('sis_usuarios_id', $data->id)->where('sis_grupo_id', $valor)->delete();
            		DB::table('sis_usuarios_grupos')->insert(
					    ['sis_usuarios_id' => $data->id, 'sis_grupo_id' => $valor]
					);
            	}	
            }
            if(property_exists($datos, "sis_usuarios_clues")){
            	DB::table('sis_usuarios_clues')->where('sis_usuarios_id', $data->id)->delete();
            	foreach($datos["sucursales"] as $valor){
            		DB::table('sis_usuarios_clues')->insert(
					    ['sis_usuarios_id' => $data->id, 'clues' => $valor]
					);
            	}
        	}		

        	if(property_exists($datos, "sis_usuarios_rfcs")){
        		$rfcs = array_filter($datos->sis_usuarios_rfcs, function($v){return $v !== null;});
        		SisUsuariosRfcs::where("sis_usuarios_id", $data->id)->delete();
        		foreach ($rfcs as $key => $value) {
        			$value = (object) $value;
        			if($value != null){
        				DB::update("update sis_usuarios_rfcs set deleted_at = null where sis_usuarios_id = $id and rfc = '$value->rfc' ");
        				$item = SisUsuariosRfcs::where("sis_usuarios_id", $data->id)->where("rfc", $value->rfc)->first();

        				if(!$item)
            				$item = new SisUsuariosRfcs;

            			$item->sis_usuarios_id = $data->id;
            			$item->tipo_persona    = $value->tipo_persona;
            			$item->razon_social    = $value->razon_social;
            			$item->rfc 			   = $value->rfc;
            			$item->paises_id 	   = $value->paises_id;
            			$item->estados_id 	   = $value->estados_id;
            			$item->municipios_id   = $value->municipios_id;
            			$item->localidad 	   = $value->localidad;
            			$item->colonia 		   = $value->colonia;
            			$item->calle 		   = $value->calle;
            			$item->numero_exterior = $value->numero_exterior;
            			$item->numero_interior = $value->numero_interior;
            			$item->codigo_postal   = $value->codigo_postal;
            			$item->email 		   = $value->email;

            			$item->save();
            		}
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
        	if(property_exists($datos, "user_clients")){
        		$medios = array_filter($datos->user_clients, function($v){return $v !== null;});
        		UsersClients::where("user_id", $data->id)->delete();
        		foreach ($medios as $key => $value) {
        			
        			if($value != null){            				
        				$item = UsersClients::where("user_id", $data->id)->where("client_id", $value)->first();

        				if(!$item)
            				$item = new UsersClients;

            			$item->user_id     = $data->id;
            			$item->client_id   = $value;         			

            			$item->save();
            		}
        		}
        	}
			$success = true;
		}  
		return $success;     						
	}

	public function contacto(){
		$datos = Input::all();
		if (empty($datos["email"]) || empty($datos["message"])) {
		    return Response::json(array("status" =>404,"mensaje" => "Error, de mensaje"),200);
		}
		//hacer verificación Captcha, asegúrese de que el remitente no es un robot :)
		$validate = \Validator::make(Input::all(), [
		    'message' => 'required',
		    'email' => 'required|email'
		]);
		if ($validate->fails()){
			return Response::json(array("status" =>404,"mensaje" => "Error, comprueba que no eres un robot"),200);
		}
			
		$data["tipo"] = $datos["reason"];
		$data["mensaje"] = $datos["message"];	
		$data["phone"] = $datos["phone"];			
		//tratar de enviar el mensaje 
		Mail::send('emails.contacto', $data, function($message) use ($datos)	{
		    $message->to($datos["email"], "Contacto Pagina")->subject('Solo Climas');
		});
		return Response::json(array("status" =>200,"mensaje" => "Hemos recibido tus comentarios, muchas gracias :)"),200);
	}
}
