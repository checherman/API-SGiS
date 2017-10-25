<?php 
namespace App\Http\Middleware;

use Closure;
use Exception;
use Request;
use Response;
use Session;
use DB;
use App\Models\Sistema\SisUsuario;

use JWTAuth, JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
* Middleware tokenPermiso
* 
* @package    plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Middleware `Token-Permiso`: Controla las peticiones a los controladores y las protege por token y permisos de usuario
*
*/
class tokenPermiso {

	/**
	 * Comprueba que el solicitante tenga un token valido y permisos para acceder al recurso solicitado.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next){
		// Obetener el recurso que se pretende acceder
		$accion = $request->route()->getAction();
		$controlador = explode('\\',$accion["controller"]);
		$controlador = explode('@',$controlador[count($controlador)-1]);
        $controlador  =$controlador[0].'.'.$controlador[1];

		try{
            $obj =  JWTAuth::parseToken()->getPayload();
            $data = SisUsuario::where("email", $obj->get('email'))->first();

            if(!$data){
                return response()->json(['error' => 'formato_token_invalido'], 401);
            }
            else{
            	if(!$data->es_super){
            		$clues = $request->header('clues');

            		$clues = DB::table('clue_usuario')->where('sis_usuarios_id', $data->id)->where('clues', $clues)->get();
            		if(!$clues)
            			return response()->json(['error' => 'autorizacion_invalido'], 401);
            	}
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expirado'], 403);
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_invalido'], 500);
        }

		/* validar que el token es enviado por la cabecera
		$token  = str_replace('Bearer ','',Request::header('Authorization'));
        if(!$token)
			return Response::json(array("status"=>400,"messages"=>"Petición incorrecta"),400);

		// validar con el servidor SALUD-ID que el token es valido y pertenezca al usuario que lo envio
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('OAUTH_SERVER').'/oauth/check/'.$token.'/'.Request::header('X-Usuario'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // Execute & get variables
        $result = json_decode(curl_exec($ch));
	   */
	    if($data){
			//if($request->get("Export"))
			//	return $next($request);

			// verificar que la sessión de sentry exista si no crearla

			try{
				/*$data = SisUsuario::where("email", Request::header("X-Usuario"))->first();
				if(!$data)
					return Response::json(["error" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => "NO ENCONTRADO"], 403);		*/
				Session::put('usuario', $data);
			}
			catch (Exception $e){
				return Response::json(["error" => "CUENTA-VALIDA-NO-AUTORIZADA", "mensaje" => $e->getMessage()], 403);
			}

			// validar que se tiene permiso al recurso solicitado si no regresar error con estado 401
			$acceso = false;
			$usuario = Session::get('usuario');
	        $usuario = SisUsuario::with("SisUsuariosGrupos")->find($usuario->id);
	        foreach($usuario->SisUsuariosGrupos as $value){
	        	$permiso = json_decode($value->permisos);
	        	if($permiso)
				foreach($permiso as $k => $v){
					if($v == 1 && $k == $controlador)
						$acceso = true;
				}
			}

	       	if (!$acceso)
				return Response::json(array("status"=>401,"messages"=>"No autorizado"),200);
	        return $next($request);
	    }
	    else
			return Response::json(array("status"=>407,"messages"=>"Autenticación requerida"),407);
	}

}
