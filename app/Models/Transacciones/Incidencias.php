<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\EstadosIncidencias;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencias extends BaseModel
{    public $incrementing = false;

    protected $table = "incidencias";
    protected $fillable = ["id", "motivo_ingreso", "impresion_diagnostica","estados_incidencias_id"];
    protected $hidden = ["updated_at", "deleted_at"];

    public function altas_incidencias()
    {
        return $this->hasMany(AltasIncidencias::class)->orderBy('id', 'DESC')->with("multimedias")->with("estados_pacientes","metodos_planificacion","turnos","tipos_altas");
    }

    public function clues()
    {
        return $this->belongsToMany(Clues::class, 'incidencia_clue', 'incidencias_id', 'clues');
    }

    public function estados_incidencias()
    {
        return $this->belongsTo(EstadosIncidencias::class);
    }

    public function movimientos_incidencias()
    {
        return $this->hasMany(MovimientosIncidencias::class)->orderBy('id', 'DESC')->with("estados_pacientes")->with("ubicaciones_pacientes")->with("triage_colores")->with("subcategorias_cie10")->with("turnos");
    }

    public function pacientes()
    {
        return $this->belongsToMany(Pacientes::class, 'incidencia_paciente', 'incidencias_id', 'pacientes_id');
    }

    public function referencias()
    {
        return $this->hasMany(Referencias::class)->orderBy('id', 'DESC')->with("multimedias","CluesOrigenO","CluesDestinoO");
    }
}