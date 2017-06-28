<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\Transacciones\Incidencias;
use Illuminate\Database\Eloquent\Model;
use App\Turnos;

class Clues extends Model
{
    protected $table = "clues";

    public function turnos()
    {
        return $this->belongsToMany(Turnos::class, 'clue_turno', 'clues_id', 'turno_id');
    }

    public function incidencias()
    {
        return $this->belongsToMany(Incidencias::class, 'incidencia_clue', 'clues', 'incidencias_id');
    }


}
