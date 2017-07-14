<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Clues;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientosIncidencias extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

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
        "estados_pacientes_id"
    ];
}