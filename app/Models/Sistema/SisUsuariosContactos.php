<?php namespace App\Models\Sistema;

use App\Models\BaseModel;
class SisUsuariosContactos extends BaseModel {

	public function SisUsuario(){
		return $this->belongsTo('App\Models\Sistema\SisUsuario','sis_usuarios_id','id');
    }
}
