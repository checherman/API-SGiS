<?php namespace App\Models\Sistema;

use App\Models\BaseModel;
use App\Models\Catalogos\Clues;

/**
* Modelo SisUsuario
*
* @package    Plataforma API
* @subpackage Controlador
* @author     Eliecer Ramirez Esquinca <ramirez.esquinca@gmail.com>
* @created    2015-07-20
*
* Modelo `SisUsuario`: Manejo de los usuarios
*
*/
class SisUsuario extends BaseModel {


	protected $hidden = ['password', 'remember_token'];

    public function SisUsuariosGrupos(){
      return $this->hasMany('App\Models\Sistema\SisUsuariosGrupos','sis_usuarios_id')
      ->join('sis_grupos', 'sis_grupos.id', '=', 'sis_usuarios_grupos.sis_grupos_id');
    }

    public function SisUsuariosDashboards(){
      return $this->hasMany('App\Models\Sistema\SisUsuariosDashboards','sis_usuarios_id')
      ->join('sis_dashboards', 'sis_dashboards.id', '=', 'sis_usuarios_dashboards.sis_dashboards_id');
    }

    public function SisUsuariosReportes(){
      return $this->hasMany('App\Models\Sistema\SisUsuariosReportes','sis_usuarios_id')
      ->join('sis_reportes', 'sis_reportes.id', '=', 'sis_usuarios_reportes.sis_reportes_id');
    }

    public function SisUsuariosClues()
    {
        return $this->belongsToMany(Clues::class, 'clue_usuario', 'sis_usuarios_id', 'clues')->with("jurisdicciones");
    }

    public function SisUsuariosRfcs(){
        return $this->hasMany('App\Models\Sistema\SisUsuariosRfcs', 'sis_usuarios_id');
    }

    public function SisUsuariosContactos(){
        return $this->hasMany('App\Models\Sistema\SisUsuariosContactos', 'sis_usuarios_id');
    }

    public function SisUsuariosNotificaciones(){
        return $this->hasMany('App\Models\Sistema\SisUsuariosNotificaciones', 'sis_usuarios_id')
        ->join('tipos_notificaciones', 'tipos_notificaciones.id', '=', 'sis_usuarios_notificaciones.tipos_notificaciones_id');
    }

    public function EstadosFuerza(){
        return $this->hasMany('App\Models\Sistema\EstadosFuerza', 'sis_usuarios_id');
    }
}