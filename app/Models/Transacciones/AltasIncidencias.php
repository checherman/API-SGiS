<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;

use App\Models\Catalogos\EstadosPacientes;
use App\Models\Catalogos\MetodoPlanificacion;
use App\Models\Catalogos\TiposAltas;
use App\Models\Catalogos\Turnos;
use App\Models\Catalogos\ValoraciionesPacientes;
use App\Models\Sistema\Multimedias;
use Illuminate\Database\Eloquent\SoftDeletes;

class AltasIncidencias extends BaseModel
{
    public $incrementing = true;

    protected $table = "altas_incidencias";
    protected $fillable = [
        "id",
        "medico_reporta_id",
        "metodos_planificacion_id",
        "tipos_altas_id",
        "turnos_id",
        "diagnostico_egreso",
        "observacion_trabajo_social",
        "clues_contrarefiere",
        "clues_regresa",
        "resumen_clinico",
        "instrucciones_recomendaciones",
    ];

    public function incidencias()
    {
        return $this->belongsTo(Incidencias::class);
    }

    public function estados_pacientes()
    {
        return $this->belongsTo(EstadosPacientes::class);
    }

    public function metodos_planificacion()
    {
        return $this->belongsTo(MetodoPlanificacion::class);
    }

    public function multimedias()
    {
        return $this->hasMany(Multimedias::class);
    }

    public function tipos_altas()
    {
        return $this->belongsTo(TiposAltas::class);
    }

    public function turnos()
    {
        return $this->belongsTo(Turnos::class);
    }

}