<?php

namespace App;

namespace App\Models\Transacciones;

class Pacientes extends Personas
{
    protected $generarID = true;

    protected $table = "pacientes";
    protected $fillable = ["id", "servidor_id", "personas_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function incidencias()
    {
        return $this->belongsToMany(Incidencias::class);
    }

    public function acompaniantes()
    {
        return $this->belongsToMany(Acompaniantes::class);
    }

    public function personas()
    {
        return $this->belongsTo(Personas::class);
    }
}
