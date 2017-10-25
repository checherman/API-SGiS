<?php
namespace App;

namespace App\Models\Transacciones;

use App\Models\BaseModel;
use App\Models\Catalogos\EstadosPacientes;
use App\Models\Catalogos\SubCategoriasCie10;
use App\Models\Catalogos\TriageColores;
use App\Models\Catalogos\Turnos;
use App\Models\Catalogos\UbicacionesPacientes;
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
        "incidencias_id",
        "medico_reporta_id",
        "estados_pacientes_id",
        "ubicaciones_pacientes_id",
        "triage_colores_id",
        "subcategorias_cie10_id",
        "turnos_id",
    ];

    public function incidencias()
    {
        return $this->belongsTo(Incidencias::class);
    }

    public function estados_pacientes()
    {
        return $this->belongsTo(EstadosPacientes::class);
    }

    public function triage_colores()
    {
        return $this->belongsTo(TriageColores::class);
    }

    public function ubicaciones_pacientes()
    {
        return $this->belongsTo(UbicacionesPacientes::class);
    }

    public function subcategorias_cie10()
    {
        return $this->belongsTo(SubCategoriasCie10::class);
    }

    public function turnos()
    {
        return $this->belongsTo(Turnos::class);
    }

}