<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriageColorTriageSintoma extends Model
{

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = false;

    protected $table = "triage_color_triage_sintoma";
    protected $fillable = ["triage_sintomas_id", "triage_colores_id", "nombre"];

}
