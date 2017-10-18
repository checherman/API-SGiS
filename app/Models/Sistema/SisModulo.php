<?php namespace App\Models\Sistema;

use App\Models\BaseModel;

/**
* Modelo SisModulo
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Modelo `SisModulo`: Manejo los permisos(modulo)
*
*/
class SisModulo extends BaseModel {
	
	public function Acciones(){
        return $this->hasMany('App\Models\Sistema\SisModuloAccion','sis_modulos_id')->orderBy('nombre', 'ASC');
    }
	public function Padres(){
        return $this->belongsTo('App\Models\Sistema\SisModulo','sis_modulos_id')->orderBy('nombre', 'ASC');
    }
	public function Hijos(){
        return $this->hasMany('App\Models\Sistema\SisModulo','sis_modulos_id')->orderBy('nombre', 'ASC');
    }
}
