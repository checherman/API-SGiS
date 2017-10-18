<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;

class CarteraServicioNivelCone extends BaseModel
{
    protected $table = "cartera_servicio_nivel_cone";
    protected $fillable = ["cartera_servicios_id", "niveles_cones_id"];


    public function carteraServicio()
    {
        return $this->belongsTo(CarteraServicios::class,'cartera_servicios_id','id')->with("items");
    }
}
