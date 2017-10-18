<?php
/**
 * Controlador Export
 * 
 * @package    plataforma API
 * @subpackage Controlador
 * @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
 * @created    2015-07-20
 */
namespace App\Http\Controllers\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Response;
use Input;
use DB; 
use Excel;
use URL;
use Session;

class ExportController extends Controller {

	/**
	 * Guardar el html a convertir en pdf.
	 *
	 * @param $request 
	 *		 
	 * @return Response
	 */
	public function setHTML(){	
		$datos 		= Input::all();
		
		$contenido 	= str_replace("id=", "class=", urldecode($datos["html"]));
		$header 	= str_replace("id=", "class=", urldecode($datos["header"]));
		$footer 	= str_replace("id=", "class=", urldecode($datos["footer"]));

		$style = file_get_contents(public_path().'/css/print.css');
		$html  = '<html>
		    <head>
		    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		    <style>
		        @page { margin: 60px 50px 35px; content: "Page " counter(page);}
		        .header { position: fixed; left: 0px; top: -45px; right: 0px; height: 50px; padding:4px;}
		        .footer { position: fixed; left: 0px; bottom: -15px; right: 0px; height: 60px; text-align: center;}
		        '.$style.'
		    </style>	    
		    </head>
		    <body>
		        <div class="header">'.$header.'</div>
		            '.$contenido.'
		        <div class="footer">'.$footer.'</div>
		    </body>
		</html>';
		
		/*Session::put("htmlTOpdf", $html);	
		Session::put("nombrepdf", $datos->get("nombre"));

		return Response::json(array("status" => 200, "messages" => "Operación realizada con exito"), 200);	
		*/

		$pdf = \App::make('dompdf.wrapper');
		$pdf->loadHTML($html);

		return $pdf->stream($datos["nombre"].".pdf");
	}

	/**
	 * Crear el PDF a partir del html.
	 *
	 * @param $request 
	 *		 
	 * @return Response
	 */
	public function getPDF(){		
		ini_set('memory_limit',   '1024M'); 
		ini_set('max_input_vars', '30000');    
	       	
    	$html   = Session::get('htmlTOpdf');
    	
    	$nombre = Session::get('nombrepdf');
    	$pdf = \App::make('dompdf.wrapper');
		$pdf->loadHTML($html);
		
		Session::forget('htmlTOpdf');
		Session::forget('nombrepdf');

		return $pdf->stream($nombre.".pdf");
	}
	/**
	 * Crear el archivo con información solicitada.
	 *
	 * @param $request 
	 *		 
	 * @return Response
	 */
	public function Export(){		
		$datos=Input::json();
		$tabla=$datos->get("tabla");
		$tipo=$datos->get("tipo");
		
		$json_data = array
		(
			"tabla"=>$tabla,
			"tipo"=>$tipo
		);
		
		$url = URL::to("/api/v1/exportGenerate");
		$type = "POST";
		$export = $this->curl($url,$json_data,$type);
		
		$fp = fopen(public_path().'/export.'.$tipo, 'w');
		fwrite($fp, $export);
		fclose($fp);
	}
	/**
	 * Genera el archivo a descargar PDF o EXCEL.
	 *
	 * @param  $request
	 *		 
	 * @return Generar documento
	 */
	public function ExportGenerate(){
		$datos=Input::all();
		$tabla=$datos["tabla"];
		$tipo=$datos["tipo"];
		
		$url = URL::to("/api/v1/".$tabla);

		$type='GET';
		
		$json_data = array
		(
			"Export"=>true
		);

		$columns = json_decode($this->curl($url,$json_data,$type));
		$columns = (array)($columns->data);
		$array=array();	
		foreach($columns as $item)
			array_push($array,(array)$item);		
		
		Excel::create(Session::get('tabla'), function($excel) use($array){
			$excel->sheet(Session::get('tabla'), function($sheet) use($array){													
				$sheet->fromArray( $array );			
			});			
		})->export($tipo);
	}
	
	/**
	 * Hace peticiones a la url solicitada.
	 *
	 * @param  $url = url para hacer la petición
	 *         $json_data = parametros a enviar
	 *         $type = tipo de metodo para la petición (POST, GET, PUT o DELETE)
	 *		 
	 * @return Valor optebido
	 */
	public function curl($url,$json_data=array(),$type){
		$token = str_replace('Bearer ','',Request::header('Authorization'));
		$user = Request::header('X-Usuario');
		
		$headers = array(
	        "Content-type: application/json;charset=\"utf-8\"",
	        "Accept: application/json",
	        "Cache-Control: no-cache",
	        "Pragma: no-cache",
	        "Authorization:Bearer $token",
			"X-Usuario:$user"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        if(count($json_data)>0)
        {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_data));  
		}

		$datos = curl_exec($ch); 
		
        if (curl_errno($ch)) 
		{
			return curl_errno($ch);
        } 
		else 
		{
        	curl_close($ch);
			return $datos;
		}
	}
}