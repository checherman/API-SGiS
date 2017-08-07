<?php

namespace App;

namespace App\Models\Transacciones;

use App\Models\Catalogos\Parentesco;

class Acompaniantes extends Personas
{
    protected $generarID = true;

    protected $table = "acompaniantes";
    protected $fillable = ["id", "servidor_id", "personas_id", "parentescos_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function pacientes()
    {
        return $this->belongsToMany(Pacientes::class);
    }

    public function parentescos()
    {
        return $this->belongsTo(Parentesco::class);
    }
}
