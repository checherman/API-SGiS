<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;

class EstadosPacientes extends BaseModel
{

    protected $table = "estados_pacientes";
    protected $fillable = ["id", "nombre", "descripcion"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];


    public function baseConocimiento()
    {
        return $this->hasMany(EstadosPacientes::class);
    }

    public function movimientos_incidencias()
    {
        return $this->hasMany(MovimientosIncidencias::class);
    }
}
