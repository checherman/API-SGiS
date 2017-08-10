<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\Sistema\Usuario;
use App\Models\Transacciones\Incidencias;
use Illuminate\Database\Eloquent\Model;

class Clues extends Model
{
    protected $table = "clues";
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function incidencias()
    {
        return $this->belongsToMany(Incidencias::class, 'incidencia_clue', 'clues', 'incidencias_id');
    }

    public function jurisdicciones()
    {
        return $this->belongsTo(Jurisdicciones::class,'jurisdicciones_id','id');
    }

    public function municipios()
    {
        return $this->belongsTo(Municipios::class);
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'clues','clues');
    }

}
