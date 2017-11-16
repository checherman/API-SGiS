<?php

namespace App;

namespace App\Models\Transacciones;

use App\Models\BaseModel;
use App\Models\Catalogos\CarteraServicios;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\Items;
use App\Models\Catalogos\Turnos;
use App\Models\Sistema\SisUsuario;

class EstadosFuerza extends BaseModel
{
    public $incrementing = true;


    protected $table = "estados_fuerza";
    protected $fillable = ["id", "clues", "turnos_id", "sis_usuarios_id"];
    protected $hidden = ["updated_at", "deleted_at"];

    public function respuesta_estados_fuerza()
    {
        return $this->hasMany(RespuestasEstadosFuerza::class)->with('cartera_servicios','items');
    }

    public function clues()
    {
        return $this->belongsTo(Clues::class,'clues','clues');
    }

    public function turnos()
    {
        return $this->belongsTo(Turnos::class);
    }

    public function sis_usuarios()
    {
        return $this->belongsTo(SisUsuario::class);
    }
}
