<?php namespace App\Models\Catalogos;

use App\Models\BaseModel;

class Estados extends BaseModel {


    public function Paises(){
		return $this->belongsTo('App\Models\Catalogos\Paises','paises_id','id');
    }

    public function Municipios(){
        return $this->hasMany('App\Models\Catalogos\Municipios','estados_id');
    }
}
