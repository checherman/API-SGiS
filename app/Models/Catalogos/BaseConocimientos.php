<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseConocimientos extends BaseModel
{
    use SoftDeletes;

    protected $generarID = true;
    protected $guardarIDServidor = true;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "base_conocimientos";
    protected $fillable = ["id", "servidor_id", "procesos", "triage_colores_id", "subcategorias_cie10_id", "valoraciones_pacientes_id", "estados_pacientes_id"];

    public function triageColor()
    {
        return $this->belongsTo(CategoriasCie10::class, 'triage_colores_id','id');
    }

    public function valoracionPaciente()
    {
        return $this->belongsTo(ValoraciionesPacientes::class,'valoraciones_pacientes_id','id');
    }

    public function subCategoriaCie10()
    {
        return $this->belongsTo(SubCategoriasCie10::class,'subcategorias_cie10_id','id');
    }

    public function estadoPaciente()
    {
        return $this->belongsTo(EstadosPacientes::class,'estados_pacientes_id','id');
    }
}
