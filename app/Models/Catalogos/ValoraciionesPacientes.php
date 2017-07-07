<?php

namespace App;

namespace App\Models\Catalogos;
use App\Models\BaseModel;
use App\Models\Transacciones\Pacientes;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValoraciionesPacientes extends BaseModel
{
    use SoftDeletes;

    protected $generarID = false;
    protected $guardarIDServidor = false;
    protected $guardarIDUsuario = false;
    public $incrementing = true;

    protected $table = "valoraciones_pacientes";
    protected $fillable = ["id", "nombre", "descripcion"];

    public function incidenciaPaciente()
    {
        return $this->hasMany(Pacientes::class,'valoraciones_pacientes_id','id');
    }
}
