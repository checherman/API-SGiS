<?php

namespace App;

namespace App\Models\Transacciones;

class Acompaniantes extends Personas
{
    protected $generarID = true;

    protected $table = "acompaniantes";
    protected $fillable = ["id", "servidor_id", "personas_id", "parentescos_id"];
}
