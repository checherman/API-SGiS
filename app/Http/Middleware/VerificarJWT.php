<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Usuario;

use JWTAuth, JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class VerificarJWT
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

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

    /**
     * Run the after request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     */
    public function terminate($request, $response)
    {
        $obj =  JWTAuth::parseToken()->getPayload();
        $usuario = Usuario::find($obj->get('id'));

        $empresas_id = Request::header("empresa");
        $sucursales_id = Request::header("sucursal");

        $mac = exec('getmac');
        $mac = explode(" ", $mac);

        //$inputs['servidor_id']  = env("SERVIDOR_ID");
        $inputs['sis_usuarios_id']  = $usuario->id;
        $inputs['ip']               = $request->ip();
        $inputs['mac']              = $mac[0];
        $inputs['tipo']             = $request->getMethod();
        $inputs['ruta']             = $request->path();
        $inputs['controlador']      = $request->segment(2);
        $inputs['tabla']            = $adminInstance->getTable();
        $inputs['peticion']         = json_encode($request);
        $inputs['respuesta']        = json_encode($response);
        $inputs['info']             = $request->server('HTTP_USER_AGENT');

        $log = App\Models\SisLogs::create($inputs);
    }

}