<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriageSintomas extends Model
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "triage_sintomas";
    protected $fillable = ["id", "nombre", "descripcion", "triage_id"];

    public function triage()
    {
        return $this->belongsTo(Triage::class,'triage_id','id');
    }

    public function triageColores()
    {
        return $this->belongsToMany(TriageColores::class, 'triage_color_triage_sintoma', 'triage_sintomas_id', 'triage_colores_id')
            ->withPivot('nombre');
    }

    public function triageColorTriageSintoma()
    {
        return $this->hasMany(TriageColorTriageSintoma::class, 'triage_sintomas_id', 'id');
    }

}
