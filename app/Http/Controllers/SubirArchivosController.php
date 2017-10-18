<?php
/**
 * Controlador Subir
 * 
 * @package    plataforma API
 * @subpackage Controlador
 * @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
 * @created    2015-07-20
 */
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Request;
use Response;
use URL;

class SubirArchivosController extends Controller{
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function subir(){
		$ext = ""; $max = ""; $nom = "";
		if(isset($_REQUEST['maximo'])){
			$max = $_REQUEST['maximo'];
		}
		
		if(isset($_REQUEST['extension'])){
			$ext = $_REQUEST['extension'];
		}

		if(isset($_REQUEST['nombre'])){
			$nom = $_REQUEST['nombre'];
		}
			
		@$ruta=$_REQUEST['ruta'];
		if(isset($_FILES["file"]))
			@$archivo = $_FILES["file"];
		else
			@$archivo = $_FILES[$_REQUEST["file"]]; 
		@$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
		
		if($ext!=""){
			if(!stripos(" ".$ext,$extension)){
				return Response::json((array("r"=>2,"msg"=>"el archivo No tiene la extension correcta")));
				die();
			}
		}
		if($max!=""){
			if ($archivo["size"] > ($max*1024)*1025){
				return Response::json((array("r"=>3,"msg"=>"el archivo Exede el limite")));
				die();
			}
		}
				
		$time = time();
		$rand = rand(1000,9999);
		$name = $nom."_".$time.$rand.".$extension";
		
		if($ruta!=""){
			$nombre=$ruta."/".$name;
		}
		if (!file_exists(public_path()."/adjunto/".$ruta)){
			mkdir(public_path()."/adjunto/".$ruta, null, true);				
		}
		if (move_uploaded_file($archivo['tmp_name'], public_path()."/adjunto/$nombre")){			
			return Response::json(array("r"=>1,"msg"=>$name));
		} 
		else {
			return Response::json(array("r"=>4,"msg"=>$name));
		}
	}

	public function mostrar(){
	    $file = $_REQUEST['file'];	
		$ruta = $_REQUEST['ruta'];
		$nombre = public_path()."/adjunto/";
		if($ruta != "")
			$nombre = $nombre."/".$ruta;
		$directorio_escaneado = scandir($nombre);
		$archivos = array();
		foreach ($file as $key => $value) {
			$archivos[] = $value;
		}
		
		if(count($archivos) > 0){
			return Response::json(array("r"=>1,"msg"=>$archivos));
		}
		else{
			return Response::json(array("r"=>0,"msg"=>"no existe"));
		}
	}

	public function eliminar(){
		$datos = (object) Input::json()->all();
    	$file=$datos->file;
		$ruta=$datos->ruta;
		
		if ($file!="") {			
			if($ruta!=""){
				$file="/adjunto/".$ruta."/".$file;
			}
			
			if (file_exists(public_path()."$file")) {
				unlink(public_path()."/$file");	
				return Response::json(array("r"=>1,"msg"=>$file));
			}
			else 
				return Response::json(array("r"=>1,"msg"=>"no existe"));
		}
  	}
}
