<?php

namespace App;

namespace App\Models\Transacciones;

class Pacientes extends Personas
{
    protected $generarID = true;

    protected $table = "pacientes";
    protected $fillable = ["id", "servidor_id", "domicilio", "personas_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

}
