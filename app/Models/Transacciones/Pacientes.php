<?php

namespace App;

namespace App\Models\Transacciones;

class Pacientes extends Personas
{
    protected $generarID = true;

    protected $table = "pacientes";
    protected $fillable = ["id", "servidor_id", "personas_id"];
    protected $hidden = ["pivot", "created_at", "updated_at", "deleted_at"];

    public function incidencias()
    {
        return $this->belongsToMany(Incidencias::class, 'incidencia_paciente', 'pacientes_id', 'incidencias_id');
    }

    public function acompaniantes()
    {
        return $this->belongsToMany(Acompaniantes::class, 'acompaniante_paciente', 'pacientes_id', 'acompaniantes_id');
    }

    public function personas()
    {
        return $this->belongsTo(Personas::class);
    }
}
