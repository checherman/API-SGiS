<?php
namespace App;

namespace App\Models\Transacciones;
use App\Models\BaseModel;
use App\Models\Catalogos\Clues;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidencias extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "incidencias";
    protected $fillable = ["id", "servidor_id", "motivo_ingreso", "impresion_diagnostica"];
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
        return $this->hasMany(MovimientosIncidencias::class)->with("estados_incidencias")->with("estados_pacientes")->with("valoraciones_pacientes")->with("triage_colores")->with("subcategorias_cie10");
    }

    public function referencias()
    {
        return $this->hasMany(Referencias::class);
    }
}