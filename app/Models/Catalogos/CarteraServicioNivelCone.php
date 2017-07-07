<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;

class CarteraServicioNivelCone extends Model
{
    protected $table = "cartera_servicio_nivel_cone";
    protected $fillable = ["cartera_servicios_id", "niveles_cones_id"];

}
