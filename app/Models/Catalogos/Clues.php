<?php

namespace App;

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use App\Turnos;

class Clues extends Model
{
    protected $table = "clues";

    public function turnos(){
        return $this->belongsToMany(Turnos::class, 'clue_turno', 'clues_id', 'turno_id');
    }


}
