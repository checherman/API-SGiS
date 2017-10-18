<?php 
/**
 * Controlador Controller
 * 
 * @package    Plataforma API
 * @subpackage Controlador
 * @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
 * @created    2015-07-20
 */
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use JWTAuth, JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

use App\Models\Sistema\SisUsuario;

use DB;

abstract class Controller extends BaseController {

	use DispatchesJobs, ValidatesRequests;	

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
	public function conceptos_recursivo_padre($padre, $familia, $id, $obra){		
		$conceptos = [];
    	$categoria = DB::select("select c.id, c.nombre, c.clave, c.costo, c.conceptos_id, 
    		(SELECT sum(total_medida) FROM servicios_conceptos WHERE servicios_id = '$id' and tipos_obras_id = '$obra' and conceptos_id = c.id ) as generador 
    		, u.nombre as unidad_medida  from conceptos c LEFT JOIN unidades_medidas u on u.id = c.unidades_medidas_id where c.deleted_at is null and c.conceptos_id = '".$padre."' and c.id in (".implode(",", $familia).") and c.deleted_at is null");    	
    	if($categoria){					
			foreach ($categoria as $k1 => $v1) {
				$conceptos[$v1->nombre] = $v1;				
				$hijos = $this->conceptos_recursivo_padre($v1->id, $familia, $id, $obra);
				if($hijos){					
					foreach($hijos as $kh => $vh) {
						$conceptos[$v1->nombre]->hijos[$kh] = $vh;
					}					
				}

			}
		}
		return (array) $conceptos;
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
    public function convertir_imagen($data, $nombre, $i){  
      			
		try{
			$data = base64_decode($data);

			$im = imagecreatefromstring($data);
			if ($im !== false) {
				$time = time().rand(11111, 99999);
				$ext = '';
				if(stripos($data, "gif"))
					$ext="gif";
				else if(stripos($data, "png"))
					$ext="png";
				else
					$ext="jpeg";
				$name = $nombre.$i."_".$time.".".$ext;
			    header('Content-Type: image/'.$ext);
			    
				if($ext == "gif")
					imagegif($im, public_path() ."/adjunto/".$nombre."/".$name);

				else if($ext == "png"){
					imagealphablending($im, false);
					imagesavealpha($im, true);
				    imagepng($im, public_path() ."/adjunto/".$nombre."/".$name);
				}
				else 
					imagejpeg($im, public_path() ."/adjunto/".$nombre."/".$name);
			    imagedestroy($im);
			    return $name;
			}
			else {
			    return null;
			}
		}catch (\Exception $e) {
			return \Response::json(["error" => $e->getMessage(), "nombre" => $nombre], 400);
        }
    }

    /**
	 * Regresa la informacion del usuario logueado.
	 *	 
	 *
	 * @return Response
	 * <code> datos del usuario logueado </code>
	 */
	public function usuarioLogueado(){
		try{
            $obj =  JWTAuth::parseToken()->getPayload();
            $data = SisUsuario::where("email", $obj->get('email'))->first();
            
            if(!$data){
                return response()->json(['error' => 'formato_token_invalido'], 401);                
            }
            else{
            	return $data;
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expirado'], 403);  
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_invalido'], 500);
        }
	}

	public function sucursales_usuario(){
		$obj =  JWTAuth::parseToken()->getPayload();

		$data = SisUsuario::where("email", $obj->get('email'))->first();

		$sucursales = [];			
		$variable = DB::table('sis_usuarios_sucursales')->where('sis_usuarios_id', $data->id)->get();
		foreach ($variable as $k1 => $v1) {
			$sucursales[] = $v1->sucursales_id;
		}	
		return $sucursales;
	}

	public function existe_sucursales_usuario($id){
		$sucursales_usuario = $this->sucursales_usuario();
		if(in_array($id, $sucursales_usuario))
			return true;
		return false;
	}

	public function empresas_usuario(){
		$obj =  JWTAuth::parseToken()->getPayload();

		$data = SisUsuario::where("email", $obj->get('email'))->first();

		$empresas = [];			
		$variable = DB::table('sis_usuarios_empresas')->where('sis_usuarios_id', $data->id)->get();
		foreach ($variable as $k1 => $v1) {
			$empresas[] = $v1->empresas_id;
		}	
		return $empresas;
	}
	
	public function existe_empresas_usuario($id){
		$empresas_usuario = $this->empresas_usuario();
		if(in_array($id, $empresas_usuario))
			return true;
		return false;
	}
}
