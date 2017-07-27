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
    protected $fillable = ["id", "servidor_id", "motivo_ingreso", "impresion_diagnostica", "estado_paciente_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function clues()
    {
        return $this->belongsToMany(Clues::class, 'incidencia_clue', 'incidencias_id', 'clues');
    }
}