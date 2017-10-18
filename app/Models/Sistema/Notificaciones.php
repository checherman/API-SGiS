<?php namespace App\Models\Sistema;

use App\Models\BaseModel;

/**
* Modelo Notificaciones
* 
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Modelo `Notificaciones`: Manejo de los usuarios
*
*/
class Notificaciones extends BaseModel {

    public function NotificacionesUsuarios(){
      return $this->hasMany('App\Models\Sistema\NotificacionesUsuarios','notificaciones_id');
    }

}