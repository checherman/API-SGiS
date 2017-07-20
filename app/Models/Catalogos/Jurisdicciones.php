<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\Transacciones\Incidencias;
use Illuminate\Database\Eloquent\Model;
use App\Turnos;

class Jurisdicciones extends Model
{
    protected $table = "jurisdicciones";

    public function clues()
    {
        return $this->hasMany(Clues::class);
    }

    public function municipios()
    {
        return $this->hasMany(Municipios::class);
    }



}
