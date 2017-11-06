<?php

namespace App;

namespace App\Models\Transacciones;

use App\Models\BaseModel;
use App\Models\Catalogos\Items;
use App\Models\Catalogos\Turnos;

class RespuestasEstadosFuerza extends BaseModel
{
    public $incrementing = true;

    protected $table = "respuestas_estados_fuerza";
    protected $fillable = ["id", "estados_fuerza_id_id", "cartera_servicios_id", "items_id", "respuesta"];
    protected $hidden = ["updated_at", "deleted_at"];


    public function items()
    {
        return $this->belongsTo(Items::class);
    }

    public function cartera_servicios()
    {
        return $this->belongsTo(EstadosFuerza::class);
    }

    public function estados_fuerza()
    {
        return $this->belongsTo(EstadosFuerza::class);
    }

    public function turnos()
    {
        return $this->belongsTo(Turnos::class);
    }


}
