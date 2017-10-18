<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\BaseModel;
use App\Turnos;
use Illuminate\Database\Eloquent\Model;

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
