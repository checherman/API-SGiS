<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\Transacciones\Incidencias;
use Illuminate\Database\Eloquent\Model;
use App\Turnos;

class Jurisdicciones extends Model
{
    public function clues()
    {
        return $this->hasMany(Clues::class,'jurisdicciones_id','id');
    }
}
