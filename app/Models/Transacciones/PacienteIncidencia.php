<?php

namespace App;

namespace App\Models\Transacciones;
use Illuminate\Database\Eloquent\Model;

class PacienteIncidencia extends Model
{
    protected $table = "paciente_incidencia";
    protected $fillable = ["pacientes_id", "incidencias_id", "acompaniantes_id"];

}
