<?php namespace App\Models\Sistema;

use App\Models\BaseModel;

/**
* Modelo SisModuloAccion
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Modelo `SisModuloAccion`: Manejo los permisos(acciones)
*
*/
class SisModuloAccion extends BaseModel {
	
    protected $table = 'sis_modulos_acciones';
    public function Modulos(){
	    return $this->belongsTo('App\Models\Sistema\SisModulo','sis_modulos_id')->orderBy('nombre', 'ASC');
	} 	
}

?>