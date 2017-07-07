<?php

namespace App;

namespace App\Models\Catalogos;
use Illuminate\Database\Eloquent\Model;

class TriageColorTriageSintoma extends Model
{
    protected $table = "triage_color_triage_sintoma";
    protected $fillable = ["triage_sintomas_id", "triage_colores_id", "nombre"];

}
