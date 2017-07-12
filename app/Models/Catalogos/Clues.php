<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\Transacciones\Incidencias;
use Illuminate\Database\Eloquent\Model;
use App\Turnos;

class Clues extends Model
{
    protected $table = "clues";

    public function incidencias()
    {
        return $this->belongsToMany(Incidencias::class, 'incidencia_clue', 'clues', 'incidencias_id');
    }

    public function jurisdiccion()
    {
        return $this->belongsTo(Jurisdicciones::class,'jurisdicciones_id','id');
    }

}
