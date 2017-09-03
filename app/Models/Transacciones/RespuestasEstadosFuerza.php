<?php

namespace App;

namespace App\Models\Transacciones;

use App\Models\BaseModel;
use App\Models\Catalogos\CarteraServicios;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\Items;
use App\Models\Catalogos\Turnos;

class RespuestasEstadosFuerza extends BaseModel
{
    protected $generarID = true;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "respuestas_estados_fuerza";
    protected $fillable = ["id", "servidor_id", "clues", "respuesta", "cartera_servicios_id","items_id", "turnos_id"];
    protected $hidden = ["updated_at", "deleted_at"];

    public function cartera_servicios()
    {
        return $this->belongsTo(CarteraServicios::class);
    }

    public function clues()
    {
        return $this->belongsTo(Clues::class,'clues','clues');
    }

    public function items()
    {
        return $this->belongsTo(Items::class);
    }

    public function turnos()
    {
        return $this->belongsTo(Turnos::class);
    }


}
