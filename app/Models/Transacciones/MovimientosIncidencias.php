<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\EstadosIncidencias;
use App\Models\Catalogos\EstadosPacientes;
use App\Models\Catalogos\TriageColores;
use App\Models\Catalogos\ValoraciionesPacientes;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientosIncidencias extends BaseModel
{
    use SoftDeletes;

    protected $generarID = true;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "movimientos_incidencias";
    protected $fillable = [
        "id",
        "servidor_id",
        "indicaciones",
        "reporte_medico",
        "diagnostico_egreso",
        "observacion_trabajo_social",
        "incidencias_id",
        "medico_reporta_id",
        "metodos_planificacion_id",
        "estados_incidencias_id",
        "valoraciones_medicas_id",
        "estados_pacientes_id",
        "triage_colores_id",
    ];

    public function incidencias()
    {
        return $this->belongsTo(Incidencias::class);
    }

    public function estados_incidencias()
    {
        return $this->belongsTo(EstadosIncidencias::class);
    }

    public function estados_pacientes()
    {
        return $this->belongsTo(EstadosPacientes::class);
    }

    public function triage_colores()
    {
        return $this->belongsTo(TriageColores::class);
    }

    public function valoraciones_pacientes()
    {
        return $this->belongsTo(ValoraciionesPacientes::class);
    }

}