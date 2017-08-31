<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Clues;
use App\Models\Catalogos\EstadosIncidencias;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencias extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "incidencias";
    protected $fillable = ["id", "servidor_id", "motivo_ingreso", "impresion_diagnostica","estados_incidencias_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function clues()
    {
        return $this->belongsToMany(Clues::class, 'incidencia_clue', 'incidencias_id', 'clues');
    }

    public function pacientes()
    {
        return $this->belongsToMany(Pacientes::class, 'incidencia_paciente', 'incidencias_id', 'pacientes_id');
    }

    public function movimientos_incidencias()
    {
        return $this->hasMany(MovimientosIncidencias::class)->orderBy('id', 'DESC')->with("estados_pacientes")->with("valoraciones_pacientes")->with("triage_colores")->with("subcategorias_cie10")->with("turnos");
    }

    public function altas_incidencias()
    {
        return $this->hasMany(AltasIncidencias::class)->orderBy('id', 'DESC')->with("estados_pacientes")->with("metodos_planificacion")->with("turnos");
    }

    public function referencias()
    {
        return $this->hasMany(Referencias::class)->orderBy('id', 'DESC');
    }

    public function estados_incidencias()
    {
        return $this->belongsTo(EstadosIncidencias::class);
    }
}