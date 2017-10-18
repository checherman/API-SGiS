<?php namespace App\Models\Catalogos;

use App\Models\BaseModel;

class Paises extends BaseModel {
	
	public function Estados(){
        return $this->hasMany('App\Models\Catalogos\Estados','paises_id');
    }
}
