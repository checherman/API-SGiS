<?php namespace App\Models\Sistema;

use App\Models\BaseModel;

/**
* Modelo SisGrupo
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Modelo `SisGrupo`: Manejo de los grupos de usuario
*
*/
class SisGrupo extends BaseModel {
	protected static $userGroupsPivot = 'sis_usuarios_grupos';
		
}