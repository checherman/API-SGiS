<?php

namespace App;

namespace App\Models\Transacciones;

use App\Models\Catalogos\Parentesco;
use App\Models\BaseModel;

class Acompaniantes extends BaseModel
{
    protected $generarID = true;

    protected $table = "acompaniantes";
    //protected $fillable = ["id", "servidor_id", "personas_id", "parentescos_id"];
    //protected $hidden = ["pivot", "created_at", "updated_at", "deleted_at"];

    public function pacientes()
    {
        return $this->belongsToMany(Pacientes::class, 'acompaniante_paciente', 'acompaniante_id', 'pacientes_id');
    }

    public function parentescos()
    {
        return $this->belongsTo(Parentesco::class);
    }

    public function personas()
    {
        return $this->belongsTo(Personas::class);
    }
}
