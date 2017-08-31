<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\EstadosIncidencias;
use App\Models\Catalogos\EstadosPacientes;
use App\Models\Catalogos\MetodoPlanificacion;
use App\Models\Catalogos\SubCategoriasCie10;
use App\Models\Catalogos\TriageColores;
use App\Models\Catalogos\Turnos;
use App\Models\Catalogos\ValoraciionesPacientes;
use Illuminate\Database\Eloquent\SoftDeletes;

class AltasIncidencias extends BaseModel
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
        "diagnostico_egreso",
        "observacion_trabajo_social",
        "medico_reporta_id",
        "metodos_planificacion_id",
        "estados_pacientes_id",
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

    public function turnos()
    {
        return $this->belongsTo(Turnos::class);
    }

    public function metodos_planificacion()
    {
        return $this->belongsTo(MetodoPlanificacion::class);
    }

}