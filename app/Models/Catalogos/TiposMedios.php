<?php namespace App\Models\Catalogos;

use App\Models\BaseModel;
use App\Models\Sistema\SisUsuariosContactos;

class TiposMedios extends BaseModel {

    public function SisUsuariosContactos()
    {
        return $this->hasMany(SisUsuariosContactos::class);
    }

}
