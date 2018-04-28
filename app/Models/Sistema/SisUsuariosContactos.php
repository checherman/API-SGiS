<?php namespace App\Models\Sistema;

use App\Models\BaseModel;
use App\Models\Catalogos\TiposMedios;

class SisUsuariosContactos extends BaseModel {

	public function SisUsuario(){
		return $this->belongsTo('App\Models\Sistema\SisUsuario','sis_usuarios_id','id');
    }

    public function TiposMedios()
    {
        return $this->belongsTo(TiposMedios::class);
    }
}
