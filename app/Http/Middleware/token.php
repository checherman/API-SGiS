<?php 
namespace App\Http\Middleware;

use Closure;
use Request;
use Response;
use Session;
use App\Models\Sistema\SisUsuario;

use JWTAuth, JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
* Middleware token
* 
* @package    plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Middleware `Token`: Controla las peticiones a los controladores y las protege por token
*
*/
class token {

	/**
	 * Comprueba que el solicitante tenga un token valido.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next){
		// validar que el token es enviado por la cabecera
		try{
            $obj =  JWTAuth::parseToken()->getPayload();
            $data = SisUsuario::where("email", $obj->get('email'))->first();
            
            if(!$data){
                return response()->json(['error' => 'formato_token_invalido'], 401);                
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expirado'], 403);  
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_invalido'], 500);
        }

        return $next($request);
	}

}
