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

class VisitasPuerperales extends BaseModel
{
    public $incrementing = true;

    protected $table = "visitas_puerperales";
    protected $fillable = [
        "id",
        "altas_incidencias_id",
        "fecha_visita",
        "seAtendio",
        "poque",
        "observaciones"
    ];

    public function altas_incidencias()
    {
        return $this->belongsTo(AltasIncidencias::class);
    }

}