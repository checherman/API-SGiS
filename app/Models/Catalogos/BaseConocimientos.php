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
    protected $fillable = ["id", "proceso", "triage_colores_id", "subcategorias_cie10_id", "ubicaciones_pacientes_id", "estados_pacientes_id"];
    protected $hidden = ["created_at", "updated_at", "deleted_at"];

    public function triageColor()
    {
        return $this->belongsTo(TriageColores::class, 'triage_colores_id','id');
    }

    public function estados_pacientes()
    {
        return $this->belongsTo(EstadosPacientes::class,'estados_pacientes_id','id');
    }

    public function subCategoriaCie10()
    {
        return $this->belongsTo(SubCategoriasCie10::class,'subcategorias_cie10_id','id');
    }

    public function ubicaciones_pacientes()
    {
        return $this->belongsTo(UbicacionesPacientes::class,'ubicaciones_pacientes_id','id');
    }
}
