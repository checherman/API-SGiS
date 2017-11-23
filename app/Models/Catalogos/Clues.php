<?php

namespace App;

namespace App\Models\Catalogos;

use App\Models\BaseModel;
use App\Models\Sistema\SisUsuario;
use App\Models\Transacciones\Incidencias;
use App\Models\Transacciones\Referencias;
use App\Models\Transacciones\RespuestasEstadosFuerza;
use Illuminate\Database\Eloquent\Model;

class Clues extends Model
{
    protected $table = "clues";
    protected $primaryKey = 'clues';
    public $incrementing = false;

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
        return $this->belongsToMany(SisUsuario::class, 'clue_usuario', 'clues', 'sis_usuarios_id');
    }

    public function respuestas_estados_fuerza()
    {
        return $this->hasMany(RespuestasEstadosFuerza::class, 'clues','clues');
    }
}
