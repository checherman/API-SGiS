<?php

namespace App;

namespace App\Models\Transacciones;

class Responsables extends Personas
{
    protected $generarID = true;

    protected $table = "responsables";
    protected $fillable = ["id", "servidor_id", "personas_id", "parentescos_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];
}
